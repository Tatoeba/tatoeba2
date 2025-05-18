<?php
namespace App\Model;

/**
 * This class is used to limit the throughput of mysql when reading
 * from a mysql streamed resultset (a.k.a. unbuffered results). In
 * such context, mysql is not only limited by its own resources, but
 * also by us, the resultset reader. If we read the resultset too
 * slowly, mysql will just have to wait until we are done reading.
 *
 * This class uses this trick to purposely slow down mysql, so that
 * mysql has free time to do other things and our query does not hog
 * all the resources. To do so, we use an
 * additive-increase/multiplicative-decrease algorithm (similar to
 * TCP congestion control algorithms). See also:
 * https://en.wikipedia.org/wiki/Additive_increase/multiplicative_decrease
 *
 ****************
 * The algorithm
 *
 * 1. We decide on a sampling period ($samplingPeriod). The algorithm
 *    will adjust the throttling on a period basis. We also decide on
 *    a rate in records per second ($targetRate). This value is
 *    initialized with $initialRate.
 *
 * 2. We wait for one period and then check how many records
 *    mysql produced ($recordsCount) compared to the rate we
 *    previously decided on ($targetRate).
 *
 * 3. We sleep() for a little while until the expected $targetRate is
 *    reached. For example, if the expected rate was 10000 records/s
 *    and mysql did produce 30000 records during a period of
 *    1 second, it means we need to wait for 2 seconds because
 *    30000/(1+2) = 10000 records/s.
 *
 * 4. We eventually adjust the value of $targetRate (see below).
 *
 * 5. Go back to step 2.
 *
 * > How is the target rate adjusted in step 4?
 *
 * The target rate value ($targetRate) is adjusted depending on
 * how well mysql performs. We calculate a performance index
 * ($perf) which is basically $actualRecords / $expectedRecords.
 *
 * If $perf is between 0 and 1, it means mysql did not reach our
 * target rate, which means mysql is getting slower. In this case,
 * we reduce the value of $targetRate by multiplying it by
 * $multiplicativeDecrease.
 *
 * If $perf is greater than 1, it means mysql is surpassing our
 * expectations in terms of rate and we have to sleep(). In this
 * case, we want to increase $targetRate value by $increase, but we
 * are only going to do so if mysql is performing very very well,
 * that is to say if $perf is greater than $increaseMinPerf.
 * Otherwise, when $perf is between 1 and $increaseMinPerf, we just
 * keep the same rate. This gives mysql some room to breathe while
 * allowing its performance to grow.
 */
class ExportRateThrottler {

    private $targetRate;
    private $recordsCount;
    private $startTime;

    private $samplingPeriod;
    private $multiplicativeDecrease;
    private $additiveIncrease;
    private $additiveIncreaseStartsFromPerf;

    private function now() : float {
        return hrtime(true) / 1000000000;
    }

    /**
     * Sleep for $waitDuration seconds,
     * even if sleeping is interrupted by a signal handler.
     */
    private function signalProofSleep(float $waitDuration) {
        $secs = (int)$waitDuration;
        $nanosecs = ($waitDuration - $secs) * 1000000000;
        $waitArray = ['seconds' => $secs, 'nanoseconds' => $nanosecs];
        do {
            $waitArray = time_nanosleep($waitArray['seconds'], $waitArray['nanoseconds']);
        } while (is_array($waitArray));
    }

    public function __construct(float $samplingPeriod = 0.5,
                                float $decrease = 0.5,
                                int   $increase = 1000,
                                float $increaseMinPerf = 2.0)
    {
        $this->samplingPeriod = $samplingPeriod;
        $this->multiplicativeDecrease = $decrease;
        $this->additiveIncrease = $increase;
        $this->additiveIncreaseMinPerf = $increaseMinPerf;
    }

    public function oneMoreRecord() {
        $this->recordsCount++;
    }

    public function start(float $initialRate = 10000) {
        $this->targetRate = $initialRate;
        $this->recordsCount = 0;
        $this->startTime = $this->now();
    }

    public function control() {
        $elapsedTime = $this->now() - $this->startTime;
        if ($elapsedTime < $this->samplingPeriod) {
            return;
        }

        $actualRecords = $this->recordsCount;
        $expectedRecords = $elapsedTime * $this->targetRate;
        $perf = $actualRecords / $expectedRecords;
        if ($perf >= $this->additiveIncreaseMinPerf) {
            $this->targetRate += $this->additiveIncrease;
        } elseif ($perf < 1.0) {
            $this->targetRate *= $this->multiplicativeDecrease;
        }

        $waitDuration = $elapsedTime * ($perf - 1);
        if ($waitDuration > 0) {
            $this->signalProofSleep($waitDuration);
        }

        $this->recordsCount = 0;
        $this->startTime = $this->now();
    }
}

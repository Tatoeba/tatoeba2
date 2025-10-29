<?php
return [
    'Queue' => [
        // time (in seconds) after which a job is requeued if the worker doesn't report back
        'defaultworkertimeout' => 120,

        // seconds of running time after which the worker will terminate (0 = unlimited)
        // 'workermaxruntime' => 20*60,
        'workermaxruntime' => 20*60 -10, # -10: temporary workaround https://github.com/Tatoeba/tatoeba2/pull/2965

        // minimum time (in seconds) which a task remains in the database before being cleaned up.
        'cleanuptimeout' => 2000,

        // number of retries if a job fails or times out.
        'defaultworkerretries' => 4,

        // seconds to sleep() when no executable job is found
        'sleeptime' => 10,

        // probability in percent of a old job cleanup happening
        'gcprob' => 10,

        // set to true for multi server setup, this will affect web backend possibilities to kill/end workers
        'multiserver' => false,

        // set this to a limit that can work with your memory limits and alike, 0 => no limit
        'maxworkers' => 2,

        // instruct a Workerprocess quit when there are no more tasks for it to execute (true = exit, false = keep running)
        'exitwhennothingtodo' => false,

        // seconds of running time after which the PHP process will terminate, null uses workermaxruntime * 100
        'workertimeout' => null,

        // determine whether logging is enabled
        'log' => true,

        // set default Mailer class
        'mailerClass' => 'Cake\Mailer\Email',

        // set default datasource connection
        'connection' => null,

        // enable Search. requires friendsofcake/search
        'isSearchEnabled' => true,

        // enable Search. requires frontend assets
        'isStatisticEnabled' => false,

        // Allow workers to wake up from their "nothing to do, sleeping" state when using QueuedJobs->wakeUpWorkers().
        // This method sends a SIGUSR1 to workers to interrupt any sleep() operation like it was their time to finish.
        // This option breaks tasks expecting sleep() to always sleep for the provided duration without interrupting.
        'canInterruptSleep' => true,
    ],
];

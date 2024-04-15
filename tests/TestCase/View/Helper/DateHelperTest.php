<?php
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\DateHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use Cake\I18n\I18n;
use Cake\I18n\Time;
use Cake\I18n\FrozenTime;

class DateHelperTest extends TestCase {

    public $DateHelper;

    private $prevLocale;

    public function setUp() {
        parent::setUp();
        $View = new View();
        $this->DateHelper = new DateHelper($View);
        $this->prevLocale = I18n::getLocale();
    }

    public function tearDown() {
        I18n::setLocale($this->prevLocale);
        unset($this->DateHelper);
        parent::tearDown();
    }

    public function agoContentProvider() {
        return [
            'null date eng' => [NULL, true, 'en', 'date unknown'],
            '0000-00-00 00:00:00 eng' => ['0000-00-00 00:00:00', false, 'en', 'date unknown'],
            'old valid date alone eng' => ['2010-01-02 12:34:56', true, 'en', 'January 2, 2010'],
            'old valid date in phrase eng' => ['2013-04-25 02:37:23', false, 'en', 'April 25, 2013'],
            'date within 30 days eng' => ['2016-06-06 14:52:11', true, 'en', "17\u{00A0}days ago"],
            'date yesterday eng' => ['2016-06-23 13:32:12', false, 'en', 'yesterday'],
            'date within last 24 hours eng' => ['2016-06-24 09:43:17', false, 'en', "4\u{00A0}hours ago"],
            'date one hour ago eng' => ['2016-06-24 12:45:34', true, 'en', 'an hour ago'],
            'date within last hour eng' => ['2016-06-24 13:06:16', true, 'en', "44\u{00A0}minutes ago"],
            'date one minute ago eng' => ['2016-06-24 13:49:20', false, 'en', 'a minute ago'],
            /*
            'null date fra' => [NULL, false, 'fr', 'date inconnue'],
            '0000-00-00 00:00:00 fra' => ['0000-00-00 00:00:00', true, 'fr', 'date inconnue'],
            'old valid date alone fra' => ['1965-02-01 11:35:36', true, 'fr', '1 février 1965 à 11:35'],
            'old valid date in phrase fra' => ['1969-05-10 21:39:21', false, 'fr', 'le 10 mai 1969 à 21:39'],
            'date within 30 days fra' => ['2016-06-20 10:23:42', true, 'fr', 'il y " 4\u{00A0}jours"],
            'date yesterday fra' => ['2016-06-23 12:32:19', true, 'fr', 'hier'],
            'date within last 24 hours fra' => ['2016-06-23 19:40:32', false, 'fr', 'il y a 18&nbsp;heures'],
            'date one hour ago fra' => ['2016-06-24 11:59:12', false, 'fr', 'il y a une heure'],
            'date within last hour fra' => ['2016-06-24 12:58:36', true, 'fr', 'il y a 52&nbsp;minutes'],
            'date one minute ago fra' => ['2016-06-24 13:50:22', true, 'fr', 'il y a une minute']
             */
        ];
    }

    /**
     * @dataProvider agoContentProvider
     */
    public function testAgo($dateTime, $alone, $locale, $expected) {
        I18n::setLocale($locale);
        Time::setTestNow(new Time('2016-06-24 13:50:43'));
        $result = $this->DateHelper->ago($dateTime, $alone);
        $this->assertEquals($expected, $result);
        Time::setTestNow();
    }

    public function formatBirthdayContentProvider() {
        $longFormat = [\IntlDateFormatter::LONG, \IntlDateFormatter::NONE];
        $shortFormat = [\IntlDateFormatter::SHORT, \IntlDateFormatter::NONE];
        return [
            'complete birthday date long format eng' => ['2013-04-30 12:21:14', $longFormat, 'en', 'April 30, 2013'],
            'complete birthday date short format eng' => ['1973-05-20 12:21:14', $shortFormat, 'en', '5/20/73'],
            'only year eng' => ['1958-00-00 12:34:56', null, 'en', '1958'],
            'month and year eng' => ['1995-04-00 00:43:21', [], 'en', 'April 1995'],
            'day and month eng' => ['0000-03-18 00:00:00', null, 'en', 'March 18'],
            /*
            'complete birthday date long format fra' => ['1956-09-18 23:45:18', $longFormat, 'fr', '18 septembre 1956'],
            'complete birthday date short format fra' => ['1966-10-11 12:21:14', $shortFormat, 'fr', '11/10/1966'],
            'only year fra' => ['2000-00-00 23:12:54', false, 'fr', '2000'],
            'month and year fra' => ['1962-12-00 22:51:12', null, 'fr', 'Décembre 1962'],
            'day and month fra' => ['0000-06-22 00:00:00', 1, 'fr', 'Juin 22']
             */
        ];
    }

    /**
     * @dataProvider formatBirthdayContentProvider
     */
    public function testFormatBirthday($dateTime, $dateFormat, $locale, $expected) {
        I18n::setLocale($locale);
        $result = $this->DateHelper->formatBirthday($dateTime, $dateFormat);
        $this->assertEquals($expected, $result);
    }

    public function getDateLabelContentProvider() {
        return [
            'created within 30 days eng' =>
            ['{createdDate}, edited {modifiedDate}', '2018-09-29 09:12:34', '2018-09-29 09:12:34', false, 'en', "25\u{00A0}days ago"],
            'created within 30 days tooltip eng' =>
            ['{createdDate}, edited {modifiedDate}', '2018-09-09 09:12:34', '2018-09-09 09:12:34', true, 'en', 'September 9, 2018 at 9:12 AM'],
            'created and modified within 30 days eng' =>
            ['{createdDate}, edited {modifiedDate}', '2018-09-29 09:12:34', '2018-10-10 01:23:45', false, 'en', "25\u{00A0}days ago, edited 14\u{00A0}days ago"],
            'created and modified within 30 days tooltip eng' =>
            ['{createdDate}, edited {modifiedDate}', '2018-09-09 09:12:34', '2018-10-10 01:23:45', true, 'en', 'September 9, 2018 at 9:12 AM, edited October 10, 2018 at 1:23 AM'],
            'created eng' =>
            ['{createdDate}, edited {modifiedDate}', '2017-09-29 09:12:34', '2017-09-29 09:12:34', false, 'en', 'September 29, 2017'],
            'created tooltip eng' =>
            ['{createdDate}, edited {modifiedDate}', '2017-09-09 09:12:34', '2017-09-09 09:12:34', true, 'en', 'September 9, 2017 at 9:12 AM'],
            'created and modified eng' =>
            ['{createdDate}, edited {modifiedDate}', '2017-09-29 09:12:34', '2017-10-10 01:23:45', false, 'en', 'September 29, 2017, edited October 10, 2017'],
            'created and modified tooltip eng' =>
            ['{createdDate}, edited {modifiedDate}', '2018-02-09 09:12:34', '2018-02-10 01:23:45', true, 'en', 'February 9, 2018 at 9:12 AM, edited February 10, 2018 at 1:23 AM'],
            'empty date eng' =>
            ['{createdDate}, edited {modifiedDate}', '0000-00-00 00:00:00', '0000-00-00 00:00:00', false, 'en', 'date unknown'],
            'empty date tooltip eng' =>
            ['{createdDate}, edited {modifiedDate}', '0000-00-00 00:00:00', '0000-00-00 00:00:00', true, 'en', 'date unknown']
        ];
    }

    /**
     * @dataProvider getDateLabelContentProvider
     */
    public function testGetDateLabel($text, $created, $modified, $tooltip, $locale, $expected)
    {
        I18n::setLocale($locale);
        Time::setTestNow(new Time('2018-10-24 17:28:36'));
        $result = $this->DateHelper->getDateLabel($text, $created, $modified, $tooltip);
        $this->assertEquals($expected, $result);
        Time::setTestNow();
    }

    public function niceContentProvider() {
        return [
            'null' => [null, 'date unknown'],
            '0000-00-00 00:00:00' => ['0000-00-00 00:00:00', 'date unknown'],
            'CakePHP Time instance' =>
                [new Time('1987-06-05 23:45:19'), 'June 5, 1987 at 11:45 PM'],
            'CakePHP FrozenTime instance' =>
                [new FrozenTime('1983-06-05 23:45:19'), 'June 5, 1983 at 11:45:19 PM UTC'],
            'string' => ['2000-12-07 01:23:45', 'December 7, 2000 at 1:23 AM']
        ];
    }

    /**
     * @dataProvider niceContentProvider
     */
    public function testNice($date, $expected)
    {
        $result = $this->DateHelper->nice($date);
        $this->assertEquals($expected, $result);
    }

    public function testAgoWorksWithEmptyObject() {
        $this->assertEquals('date unknown', $this->DateHelper->ago(null));
    }

    public function testAgoWorksWithStrings() {
        $this->assertEquals('date unknown', $this->DateHelper->ago('0000-00-00 00:00:00'));
        $this->assertEquals('date unknown', $this->DateHelper->ago(''));
        $this->assertEquals('date unknown', $this->DateHelper->ago('2017-03-04'));
        $expected = 'March 5, 2004';
        $this->assertEquals($expected, $this->DateHelper->ago('2004-03-05 09:27:00'));
    }

    public function testAgoWorksWithDateTimeObjects() {
        $expected = 'November 23, 1988';
        $this->assertEquals($expected, $this->DateHelper->ago(new Time('1988-11-23 13:45:00')));
    }

    public function testAgoWorksWithFrozenTimeObjects() {
        $expected = 'November 24, 1988';
        $this->assertEquals($expected, $this->DateHelper->ago(new FrozenTime('1988-11-24 13:45:00')));
    }
}

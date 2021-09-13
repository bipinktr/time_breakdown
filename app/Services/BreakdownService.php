<?php

namespace App\Services;

define('OneMonth', 24 * 3600 * 30);//2592000
define('OneDay', 24 * 3600);//86400
define('OneHour', 3600);
define('OneMinute', 60);

class BreakdownService
{
    /**
     * @var int[]
     */
    private $months = [];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->months = [31, 28, 31, 30, 31, 30,
            31, 31, 30, 31, 30, 31];
    }

    //

    function countLeapYears($year, $month): float
    {
        $years = $year;
        // To verify if the current year needs to be considered
        // for the count of leap years or not
        if ($month <= 2) {
            $years--;
        }
        // Leap year if it is a multiple of 4,
        // multiple of 400 and not a multiple of 100.
        return floor($years / 4) - floor($years / 100) +
            floor($years / 400);
    }

    function getTotalDays($date): float
    {
        $year = date('Y', $date);
        $month = date('m', $date);
        $day = date('d', $date);
        $hour = date('H', $date);
        $minute = date('i', $date);
        $second = date('s', $date);
        // initialize count using year, day, hour, minute and second
        $days = $year * 365 + $day + $hour / 24 + $minute / (24 * 60) + $second / (24 * 3600);

        // Add days for months in given date
        for ($i = 0; $i < $month - 1; $i++) {
            $monthDays = $this->months[$i];
            // Assume that a month is always 30 days, instead of using calendar months
            if ($monthDays > 30) {
                $monthDays = 30;
            }
            $days += $monthDays;
        }

        // Since every leap year is of 366 days,
        // Add a day for every leap year
        $days += $this->countLeapYears($year, $month);
        // return total days
        return $days;
    }

    function getDifferenceInSeconds($startDate, $endDate): float
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        // count total number of days before start date
        $startDays = $this->getTotalDays($startDate);
        // count total number of days before end date
        $endDays = $this->getTotalDays($endDate);
        // return difference between two counts in seconds
        return round(($endDays - $startDays) * 86400);
    }

    function splitExpression($expression): array
    {
        // split expression to extract time unit and integer
        $expressionSplit = str_split($expression);
        $timeUnit = '';
        $integer = 1; // default 1 if no integer set
        if (count($expressionSplit) == 2) {
            $timeUnit = $expressionSplit[1];
            $integer = $expressionSplit[0];
        } else if (count($expressionSplit) == 1) {
            $timeUnit = $expressionSplit[0];
        }
        return ['time_unit' => $timeUnit, 'integer' => $integer];
    }

    function convertSecondsToBreakdown($n, $timeExpressions): string
    {
        $breakdown = [];
        // sort time expressions based on integer
        sort($timeExpressions);
        $order = ['m', 'd', 'h', 'i', 's'];
        // sort time expressions based on the above order of time unit
        uksort($timeExpressions, function ($key1, $key2) use ($order, $timeExpressions) {
            $expressionSplit1 = $this->splitExpression($timeExpressions[$key1]);
            $timeUnit1 = $expressionSplit1['time_unit'];
            $expressionSplit2 = $this->splitExpression($timeExpressions[$key2]);
            $timeUnit2 = $expressionSplit2['time_unit'];
            return ((array_search($timeUnit1, $order) > array_search($timeUnit2, $order)) ? 1 : -1);
        });
        // loop through input expressions and
        // find breakdown in month, day, hour, minute and second from seconds
        foreach ($timeExpressions as $expression) {
            $expressionSplit = $this->splitExpression($expression);
            $timeUnit = $expressionSplit['time_unit'];
            $integer = $expressionSplit['integer'];
            switch ($timeUnit) {
                case 'm': // month
                    $mDiv = OneMonth * $integer;
                    $breakdown[$expression] = floor($n / $mDiv);
                    $n = ($n % $mDiv);
                    break;
                case 'd': // day
                    $dDiv = OneDay * $integer;
                    $breakdown[$expression] = floor($n / $dDiv);
                    $n = ($n % $dDiv);
                    break;
                case 'h': // hour
                    $hDiv = OneHour * $integer;
                    $breakdown[$expression] = round($n / $hDiv, 2);
                    $n %= $hDiv;
                    break;
                case 'i': // minute
                    $iDiv = OneMinute * $integer;
                    $breakdown[$expression] = round($n / $iDiv, 2);
                    $n %= $iDiv;
                    break;
                case 's': // second
                    $breakdown[$expression] = round($n / $integer, 2);
                    break;
            }
        }
        // return string format of breakdown
        return http_build_query($breakdown, '', ', ');
    }

}

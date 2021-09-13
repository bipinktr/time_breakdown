<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Services\BreakdownService;

class BreakdownTest extends TestCase
{

    /**
     * test Breakdown response
     *
     * @return void
     */
    public function testBreakdown()
    {
        $breakdownService = new BreakdownService();
        $startDate = "2020-01-01T00:00:00";
        $endDate = "2020-03-01T12:30:00";
        $timeExpressions = ["2m", "m", "d", "2h"];
        $breakdownResult = "2m=0, m=1, d=29, 2h=6.25";
        // difference between start date and end date in seconds
        $seconds = $breakdownService->getDifferenceInSeconds($startDate, $endDate);
        // breakdown in month, day, hour, minute and second
        $breakdown = $breakdownService->convertSecondsToBreakdown($seconds, $timeExpressions);

        $this->assertEquals(
            $breakdownResult, $breakdown
        );
    }
}

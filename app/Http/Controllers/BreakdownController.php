<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\Breakdown;
use App\Services\BreakdownService;
use Illuminate\Http\Request;

class BreakdownController extends BaseController
{
    /**
     * @var BreakdownService
     */
    private $breakdownService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BreakdownService $breakdownService)
    {
        $this->breakdownService = $breakdownService;
    }

    //
    public function createBreakdown(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'time_expressions' => 'required|array',
            'time_expressions.*' => 'required|string'

        ]);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $timeExpressions = $request->input('time_expressions');
        // difference between start date and end date in seconds
        $seconds = $this->breakdownService->getDifferenceInSeconds($startDate, $endDate);
        // breakdown in month, day, hour, minute and second
        $breakdown = $this->breakdownService->convertSecondsToBreakdown($seconds, $timeExpressions);
        //DB Insert breakdown in month, day, hour, minute and second for start date and end date
        $breakdownInserted = Breakdown::create(['start_date' => $startDate, 'end_date' => $endDate, 'breakdown' => $breakdown]);
        return response()->json($breakdownInserted, 201);
    }

    public function filterBreakdown(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        //Get list of breakdowns for start date and end date
        $breakdowns = Breakdown::where([['start_date', '=', $startDate], ['end_date', '=', $endDate]])->get();
        return response()->json($breakdowns);
    }

}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimesheetStoreRequest;
use App\Http\Requests\TimesheetUpdateRequest;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /**
         * @var User The logged user.
         */
        $user = Auth::user();

        // Every user gets its profiled timesheets research: Admin can search between all timesheets OR can filter by User; User can see only its timesheets.
        $userFilter = $user->isAdmin() ? $request->get('user') : $user->id;

        // The Query parameters
        $projectFilter = $request->get('project');
        $activityFilter = $request->get('activity');
        $dateFrom = $request->get('dateFrom');
        $dateTo = $request->get('dateTo');

        // IF there is a userFilter (User is not Admin OR User is Admin and wants to filter results by User), filter the results, otherwise don't filter by User.
        $query = $userFilter ? Timesheet::where('user_id', $userFilter) : Timesheet::select();

        // Building the query
        if ($projectFilter) {
            $query->where('project_id', $projectFilter);
        }
        if ($activityFilter) {
            $query->where('activity_id', $activityFilter);
        }
        if ($dateFrom) {
            $query->where('date', '>', $dateFrom);
        }
        if ($dateTo) {
            $query->where('date', '<', $dateTo);
        }
        $timesheets = $query->paginate(10);

        return response()->json($timesheets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TimesheetStoreRequest $request)
    {
        $data = $request->all();

        $data['user_id'] = $request->user()->id;

        $timesheet = Timesheet::create($data);
        return response()->json($timesheet);
    }

    /**
     * Display the specified resource.
     * For now no show is implemented.
     */
    public function show(string $id)
    {
        return response(status: 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TimesheetUpdateRequest $request, string $id)
    {
        $timesheet = Timesheet::find($id);
        if (!$timesheet) {
            return response()->json(['errors' => 'Timesheet not found'], 404);
        }

        $timesheet->update($request->all());

        return response()->json($timesheet);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return response(status: 404);
        // Timesheets should be deletable.
    }
}

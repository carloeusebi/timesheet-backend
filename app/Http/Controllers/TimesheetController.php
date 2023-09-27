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


        // The Query parameters
        $userFilter = $request->get('employee'); //param name is employee, to improve query string readability
        $projectFilter = $request->get('project');
        $activityFilter = $request->get('activity');
        $dateFrom = $request->get('dateFrom');
        $dateTo = $request->get('dateTo');

        // Every user gets its profiled timesheets research: Admin can search between all timesheets OR can filter by User; User can see only its timesheets.
        $query = $user->isAdmin() ? Timesheet::select() : Timesheet::where('user_id', $user->id);

        $query->with('user', 'project', 'activity');

        // Building the query
        if ($userFilter && $user->isAdmin()) {
            $query->whereRelation('user', 'name', 'like', "%$userFilter%");
        }
        if ($projectFilter) {
            $query->whereRelation('project', 'name', 'like', "%$projectFilter%");
        }
        if ($activityFilter) {
            $query->whereRelation('activity', 'name', 'like', "%$activityFilter%");
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

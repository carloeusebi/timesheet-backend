<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimesheetStoreRequest;
use App\Http\Requests\TimesheetUpdateRequest;
use App\Http\Resources\TimesheetCollection;
use App\Http\Resources\TimesheetResource;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends Controller
{
    /**
     * List the Timesheets.
     * 
     * Returns all the User associated Timesheets. If User is Admin retruns all the Timesheets.
     * 
     * @queryParam filters[employee] Filters by employee name. Example: Carlo Eusebi
     * @queryParam filters[project] Filters by project name. Example: Youvolution
     * @queryParam filters[activity] Filters by activity name. Example: Development
     * @queryParam filters[dateFrom] Filters by date. Example: 2023-28-09
     * @queryParam filters[dateTo] Filters by date. Example: 2023-29-09
     * 
     * @apiResourceCollection App\Http\Resources\TimesheetCollection
     * @apiResourceModel App\Models\Timesheet paginate=10
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
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = Timesheet::orderBy('activity_start', 'desc');
        // Every user gets its profiled timesheets research: Admin can search between all timesheets OR can filter by User; User can see only its timesheets.

        if (!$user->isAdmin())
            $query->where('user_id', $user->id);

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
            $query->where('activity_start', '>', $dateFrom);
        }
        if ($dateTo) {
            $query->where('activity_end', '<', $dateTo);
        }
        $timesheets = $query->paginate(10);

        return new TimesheetCollection($timesheets);
    }

    /**
     * Log a new timesheet.
     * 
     * @bodyParam project_id string required The ID of the Project. Example: 1
     * @bodyParam activity_id string required The ID of the Activity. Example: 1
     * 
     * @response 422 {["error": "message"]}
     * @apiResource App\Http\Resources\TimesheetResource
     * @apiResourceModel App\Models\Timesheet
     */
    public function store(TimesheetStoreRequest $request)
    {
        $data = $request->all();

        $data['user_id'] = $request->user()->id;

        $timesheet = Timesheet::create($data);
        return new TimesheetResource($timesheet);
    }

    /**
     * Display the specified Timesheet. 
     * 
     * @response 404 {"errors": "Timesheet not found"}
     * 
     * @apiResource App\Http\Resources\TimesheetResource
     * @apiResourceModel App\Models\Timesheet
     */
    public function show(string $id)
    {
        /**
         * @var User
         */
        $user = Auth::user();
        $timesheet = $user->isAdmin() ? Timesheet::find($id) : Timesheet::where('user_id', $user->id)->find($id);

        if (!$timesheet) {
            return response()->json(['errors' => 'Timesheet not found'], 404);
        }

        return new TimesheetResource($timesheet);
    }

    /**
     * Updates an existing Timesheet.
     * 
     * @bodyParam project_id string required The ID of the Project. Example: 1
     * @bodyParam activity_id string required The ID of the Activity. Example: 1
     * 
     * @response 404 {"errors": "Timesheet not found"}
     * @response 422 {["error": "message"]}
     * 
     * @apiResource App\Http\Resources\TimesheetResource
     * @apiResourceModel App\Models\Timesheet
     */
    public function update(TimesheetUpdateRequest $request, string $id)
    {
        $timesheet = Timesheet::find($id);
        if (!$timesheet) {
            return response()->json(['errors' => 'Timesheet not found'], 404);
        }

        // updates the relations
        $timesheet->update($request->all());
        $timesheet->load('activity', 'project');

        return new TimesheetResource($timesheet);
    }
}

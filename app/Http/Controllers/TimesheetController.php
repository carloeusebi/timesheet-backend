<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimesheetStoreRequest;
use App\Http\Requests\TimesheetUpdateRequest;
use App\Http\Resources\TimesheetCollection;
use App\Http\Resources\TimesheetResource;
use App\Models\Timesheet;
use Carbon\Carbon;
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

        $timezone = config('app.timezone');

        // The Query parameters
        $userFilter = $request->get('employee'); //param name is employee, to improve query string readability
        $projectFilter = $request->get('project');
        $activityFilter = $request->get('activity');
        $dateFrom = Carbon::parse($request->get('date_from'))->timezone($timezone);
        $dateTo = Carbon::parse($request->get('date_to'))->timezone($timezone);
        $perPage = $request->get('per_page') ?? 10;

        $column = $request->get('order_by') ?? 'date';
        $direction = $request->get('direction') ?? 'desc';

        if ($column === 'user') $column = 'users.name';

        $query = Timesheet::orderBy($column, $direction);
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
        if ($request->date_from) {
            $query->where('date', '>=', $dateFrom);
        }
        if ($request->date_to) {
            $query->where('date', '<=', $dateTo);
        }

        $timesheets = $query->paginate($perPage);

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

        $data['user_id'] ??= $request->user()->id;

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
        $timesheet = Timesheet::find($id);

        if (!$timesheet) {
            return response()->json(['errors' => 'Timesheet not found'], 404);
        }

        $this->authorize('view', $timesheet);

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

        $this->authorize('update', $timesheet);

        return new TimesheetResource($timesheet);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Block every method with the `\App\Middleware\AuthenticateAdmin` middleware except the index method.
     */
    public function __construct()
    {
        $this->middleware('auth.admin')->except(['index']);
    }

    /**
     * Returns a list of Projects.
     * 
     * Returns all the Users associated Projects. If User is Admin returns all the Projects.
     * 
     */
    public function index(Request $request)
    {
        /**
         * @var User
         */
        $user = Auth::user();

        $defaultPerPage = 10;

        // if user is employee (user) return only the projects assigned to him.
        $query = $user->isAdmin() ? Project::select() : Project::whereRelation('users', 'users.id', $user->id);

        $orderBy = $request->order_by;
        $direction = $request->direction;

        if ($orderBy && $direction) $query->orderBy($orderBy, $direction);

        $perPage = $request->per_page ?? $defaultPerPage;

        $projects = $query->with('users')->paginate($perPage);

        return new ProjectCollection($projects);
    }

    /**
     * Create a new Project.
     */
    public function store(ProjectStoreRequest $request)
    {
        $project = Project::create($request->only('name'));

        $project->activities()->attach($request->activity_ids);
        $project->users()->attach($request->user_ids);

        return new ProjectResource($project);
    }

    /**
     * Display the specified Project.
     */
    public function show(string $id)
    {
        $project = Project::with('users', 'activities')->findOrFail($id);
        return new ProjectResource($project);
    }

    /**
     * Update an existing Project.
     */
    public function update(ProjectUpdateRequest $request, string $id)
    {
        $project = Project::findOrFail($id);

        $project->update($request->only('name'));

        $project->activities()->sync($request->activity_ids);
        $project->users()->sync($request->user_ids);

        return new ProjectResource($project);
    }

    /**
     * Remove an existing Project.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return response(status: 204);
    }

    public function calculateHours(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date',
        ]);

        $timezone = config('app.timezone');
        $dateRange = [];
        $dateRange['from'] = Carbon::parse($request->date_from)->timezone($timezone);
        $dateRange['to'] = Carbon::parse($request->date_to)->timezone($timezone);

        $projects = Project::all();

        $projects->each(function ($project) use ($dateRange) {
            $project->activities->each(function ($activity) use ($dateRange, $project) {

                $activity->hours = $project->timesheets()
                    ->where('activity_id', $activity->id)
                    ->whereBetween('date', [$dateRange['from'], $dateRange['to']])
                    ->sum('hours');
            });
        });


        return ProjectResource::collection($projects);
    }
}

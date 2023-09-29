<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Models\Project;
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
     */
    public function index()
    {
        /**
         * @var User
         */
        $user = Auth::user();

        // if user is employee (user) return only the projects assigned to him.
        $projects = $user->isAdmin() ? Project::all() : Project::whereRelation('users', 'users.id', $user->id)->get();
        return response()->json($projects);
    }

    /**
     * Create a new Project.
     */
    public function store(ProjectStoreRequest $request)
    {
        $project = Project::create($request->only('name'));

        $project->activities()->attach($request->activity_ids);
        $project->users()->attach($request->user_ids);

        return response()->json($project->load('users')->load('activities'), 201);
    }

    /**
     * Display the specified Project.
     */
    public function show(string $id)
    {
        $project = Project::with('users')->findOrFail($id);
        return response()->json($project);
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

        return response()->json($project->load('users')->load('activities'));
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
}

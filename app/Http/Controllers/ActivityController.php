<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityCollection;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;

class ActivityController extends Controller
{
    /**
     * Block every method with the `\App\Middleware\AuthenticateAdmin` middleware except the index method.
     */
    public function __construct()
    {
        $this->middleware('auth.admin');
    }

    /**
     * List all the activities.
     */
    public function index(Request $request)
    {
        $defaultPerPage = 10;
        $perPage = $request->per_page ?? $defaultPerPage;

        $query = Activity::select();

        $orderBy = $request->order_by;
        $direction = $request->direction;

        if ($orderBy && $direction) $query->orderBy($orderBy, $direction);

        $activities = $query->paginate($perPage);

        return new ActivityCollection($activities);
    }

    /**
     * Create a new Activity.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:activities,name'
        ]);

        $activity = Activity::create($request->only('name'));

        return new ActivityResource($activity);
    }

    /**
     * Display the specified Activity.
     */
    public function show(string $id)
    {
        $activity = Activity::findOrFail($id);

        return new ActivityResource($activity);
    }

    /**
     * Update at existing Activity.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => "required|unique:activities,name,$id"
        ]);

        $activity = Activity::findOrFail($id);
        $activity->update($request->only('name'));

        return new ActivityResource($activity);
    }

    /**
     * Delete the specified Activity.
     */
    public function destroy(string $id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();
        return response(status: 204);
    }
}

<?php

namespace App\Http\Controllers;

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
        $this->middleware('auth.admin')->except(['index']);
    }

    /**
     * List all the activities.
     */
    public function index()
    {
        $activities = Activity::all();
        return response()->json($activities);
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

        return response()->json($activity, 201);
    }

    /**
     * Display the specified Activity.
     */
    public function show(string $id)
    {
        $activity = Activity::findOrFail($id);

        return response()->json($activity);
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

        return response($activity);
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

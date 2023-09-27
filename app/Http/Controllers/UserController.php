<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Role;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Block every method with the `\App\Middleware\AuthenticateAdmin` middleware except the index method.
     */
    public function __construct()
    {
        $this->middleware('auth.admin')->except(['index']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::whereRelation('role', 'role', 'user')->get();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $data = $request->all();

        //generate a default password
        $password = str_replace(' ', '.', strtolower($request->name));
        $data['password'] = bcrypt($password);

        // assign the user role id
        $userRoleId = Role::where('role', 'user')->pluck('id')->first();
        $data['role_id'] = $userRoleId;

        $user = User::create($data);

        return response()->json($user->load('role'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // this is not used at the moment.
        return response(status: 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->only('name', 'email'));
        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response(status: 204);
    }
}

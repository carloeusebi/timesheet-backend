<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Block every method with the `\App\Middleware\AuthenticateAdmin` middleware except the index method.
     */
    public function __construct()
    {
        $this->middleware('auth.admin')->except('index');
    }

    /**
     * Return a list of the the Users.
     *
     */
    public function index(Request $request)
    {
        $defaultPerPage = 10;

        /**
         * @var User
         */
        $user = Auth::user();

        $query = User::select();

        $orderBy = $request->input('order_by');
        $direction = $request->input('direction');

        if ($orderBy && $direction) $query->orderBy($orderBy, $direction);

        if (!$user->isAdmin()) $query->where('id', $user->id);

        $nameFilter = $request->name ?? null;
        if ($nameFilter) $query->where('name', 'like', "%$nameFilter%");

        $perPage = $request->per_page ?? $defaultPerPage;

        $users = $query->paginate($perPage);

        return new UserCollection($users);
    }

    /**
     * Create a new User.
     */
    public function store(UserStoreRequest $request)
    {
        $data = $request->all();

        //generate a default password
        // Password is generated by default based on the first and last names of the User.
        $password = str_replace(' ', '.', strtolower($request->name));
        $data['password'] = bcrypt($password);

        $user = User::create($data);

        return new UserResource($user);
    }

    /**
     * Return the specified User.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id)->load('projects:id,name');
        return new UserResource($user);
    }

    /**
     * Update an existing user.
     */
    public function update(UserUpdateRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->only('name', 'email', 'role_id'));
        return response()->json($user);
    }

    /**
     * Remove the specified User.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response(status: 204);
    }
}

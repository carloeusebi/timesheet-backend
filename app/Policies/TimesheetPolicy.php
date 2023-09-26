<?php

namespace App\Policies;

use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TimesheetPolicy
{
    // /**
    //  * Determine whether the user can view any models.
    //  */
    // public function viewAny(User $user): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Timesheet $timesheet): Response
    {
        return $user->id === $timesheet->user_id
            ? Response::allow()
            : Response::denyWithStatus(401);
    }

    // /**
    //  * Determine whether the user can create models.
    //  */
    // public function create(User $user): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Timesheet $timesheet): Response
    {
        return $user->id === $timesheet->user_id
            ? Response::allow()
            : Response::denyWithStatus(401);
    }
}

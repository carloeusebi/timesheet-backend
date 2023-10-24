<?php

namespace App\Http\Requests;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class TimesheetStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Removes the id form the field name to make the names more user friendly.
     */
    public function attributes()
    {
        return Timesheet::labels();
    }

    public function prepareForValidation()
    {
        $this->merge([
            'userId' => $this->user_id,
            'projectId' => $this->project_id,
            'activityId' => $this->activity_id
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'userId' => 'nullable|exists:users,id|',
            'projectId' => 'required|exists:projects,id',
            'activityId' => 'required|exists:activities,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.5,max:10',
            'description' => 'required'
        ];
    }

    /**
     * Performs additional validation.
     * 
     * Checks that the Activity-Project relation exists for the chosen activity.
     */
    public function withValidator(Validator $validator)
    {
        if ($validator->errors()->all()) return;

        $validator->after(function ($validator) {
            $userId = $this->input('userId') ?? Auth::user()->id;
            $projectId = $this->input('project_id');
            $activityId = $this->input('activity_id');

            $project = Project::find($projectId);
            $user = User::find($userId);
            $activity = Activity::find($activityId);

            $activityProjectRelationExists = $project->activities()->where('activity_id', $activityId)->count();
            if (!$activityProjectRelationExists) {
                $validator->errors()->add('activityId', "{$activity->name} non esiste su {$project->name}");
            }

            $projectUserRelationExists = $user->projects()->where('project_id', $projectId)->count();
            if (!$projectUserRelationExists) {
                $validator->errors()->add('projectId', "{$user->name} non è assegnato a {$project->name}.");
            }
        });
    }
}

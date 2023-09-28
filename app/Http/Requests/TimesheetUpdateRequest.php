<?php

namespace App\Http\Requests;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Timesheet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class TimesheetUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $timesheet = Timesheet::find($this->route('timesheet'));
        return $timesheet && $this->user()->can('update', $timesheet);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => 'required|exists:projects,id',
            'activity_id' => 'required|exists:activities,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:1|max:8',
            'description' => 'required'
        ];
    }

    /**
     * Removes the id form the field name to make the names more user friendly.
     */
    public function attributes()
    {
        return [
            'project_id' => 'project',
            'activity_id' => 'activity',
        ];
    }

    /**
     * Performs additional validation.
     * 
     * Checks that the Activity-Project relation exists for the chosen activity.
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $projectId = $this->input('project_id');
            $activityId = $this->input('activity_id');

            $relationExists = Project::find($projectId)->activities()->where('activity_id', $activityId)->count();

            if (!$relationExists) {
                $validator->errors()->add('activity_id', 'Activity doesn\'t exists on Project');
            }
        });
    }
}

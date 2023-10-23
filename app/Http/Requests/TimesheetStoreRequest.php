<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\Timesheet;
use Illuminate\Foundation\Http\FormRequest;
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
            $projectId = $this->input('project_id');
            $activityId = $this->input('activity_id');

            $relationExists = Project::find($projectId)->activities()->where('activity_id', $activityId)->count();

            if (!$relationExists) {
                $validator->errors()->add('activity_id', 'Activity doesn\'t exists on Project');
            }
        });
    }
}

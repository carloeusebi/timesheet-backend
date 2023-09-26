<?php

namespace App\Http\Requests;

use App\Models\Timesheet;
use Illuminate\Foundation\Http\FormRequest;

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
}

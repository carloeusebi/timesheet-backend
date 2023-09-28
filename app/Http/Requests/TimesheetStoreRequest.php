<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'project_id' => 'project',
            'activity_id' => 'activity',
        ];
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

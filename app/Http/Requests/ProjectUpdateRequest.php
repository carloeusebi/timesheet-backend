<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ProjectUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        return $user->isAdmin();
    }

    public function prepareForValidation()
    {
        $this->merge([
            'userIds' => $this->user_ids,
            'activityIds' => $this->activity_ids
        ]);
    }

    public function attributes()
    {
        return [
            'name' => 'Nome',
            'userIds' => 'Impiegati',
            'activityIds' => 'Attività'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = request()->route('project');
        return [
            'name' => "required|unique:projects,name,$id", //name is unique, but ignores self
            'userIds' => 'nullable|exists:users,id',
            'activityIds' => 'nullable|exists:activities,id'
        ];
    }
}

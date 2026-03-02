<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $sportId = $this->route('sport')?->sport_id;

        return [
            'name' => 'required|string|max:30|unique:sports,name' . ($sportId ? ",$sportId,sport_id" : ''),
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The sport name is required.',
            'name.unique' => 'A sport with this name already exists.',
            'name.max' => 'The sport name must not exceed 30 characters.',
        ];
    }
}

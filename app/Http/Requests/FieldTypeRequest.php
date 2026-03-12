<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FieldTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // auth middleware handles this
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $fieldTypeId = $this->route('fieldType')?->field_type_id;

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('field_types', 'name')->ignore($fieldTypeId, 'field_type_id'),
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The field type name is required.',
            'name.unique' => 'A field type with this name already exists.',
            'name.max' => 'The field type name must not exceed 50 characters.',
        ];
    }
}
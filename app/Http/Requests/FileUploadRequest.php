<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Constants\FileMessages;
use Illuminate\Validation\Rule;

class FileUploadRequest extends FormRequest
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
        return [
            'file' => [
                'required',
                'file',
                'max:10240', // 10 MB in KB
                'mimes:jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx',
            ],
            'category' => [
            'required',
            'integer',
            Rule::exists('file_categories', 'file_category_id'),
        ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'file.required' => FileMessages::FILE_REQUIRED,
            'file.file' => FileMessages::FILE_INVALID,
            'file.max' => FileMessages::FILE_TOO_LARGE,
            'file.mimes' => FileMessages::FILE_TYPE_NOT_ALLOWED,
            'category.required' => FileMessages::CATEGORY_REQUIRED,
            'category.exists' => FileMessages::CATEGORY_INVALID,
        ];
    }
}

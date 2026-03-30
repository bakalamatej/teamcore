<?php

namespace App\Http\Requests;

use App\Constants\FileMessages;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FileUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
                'max:10240',
                'mimes:heic,jpeg,jpg,png,gif,webp,pdf,doc,docx,xls,xlsx',
            ],
            'file_category_id' => [
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

            'file_category_id.required' => FileMessages::CATEGORY_REQUIRED,
            'file_category_id.integer' => FileMessages::CATEGORY_INVALID,
            'file_category_id.exists' => FileMessages::CATEGORY_INVALID,
        ];
    }
}
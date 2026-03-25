<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\ResultType;

class EventResultsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $resultTypeValues = array_map(fn(ResultType $type) => $type->value, ResultType::cases());

        return [
            'club_value' => 'nullable|string|max:20',
            'club_result_type' => ['nullable', Rule::in($resultTypeValues)],
            'club_ranking' => 'nullable|integer|min:1',
            'club_note' => 'nullable|string|max:1000',

            'members' => 'nullable|array',
            'members.*.value' => 'nullable|string|max:20',
            'members.*.result_type' => ['nullable', Rule::in($resultTypeValues)],
            'members.*.ranking' => 'nullable|integer|min:1',
            'members.*.note' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'club_ranking.integer' => 'Club ranking must be a whole number.',
            'club_ranking.min' => 'Club ranking must be at least 1.',
            'members.*.ranking.integer' => 'Member ranking must be a whole number.',
            'members.*.ranking.min' => 'Member ranking must be at least 1.',
        ];
    }
}
<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventResultsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'club_score' => 'nullable|numeric|min:0',
            'club_ranking' => 'nullable|integer|min:1',
            'club_note' => 'nullable|string|max:1000',
            'members' => 'nullable|array',
            'members.*.score' => 'nullable|numeric|min:0',
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
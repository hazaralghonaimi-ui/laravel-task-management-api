<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:40',
            'description' => 'sometimes|nullable|string',
            'priority' => 'sometimes|integer|between:1,5'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a title.',
            'title.string' => 'The title must be text.',
            'title.max' => 'The title cannot be longer than 40 characters.',

            'description.string' => 'The description must be text.',

            'priority.required' => 'Please choose a priority.',
            'priority.integer' => 'The priority must be a number.',
            'priority.min' => 'The priority must be at least 1.',
            'priority.max' => 'The priority cannot be greater than 5.',
        ];
    }
}

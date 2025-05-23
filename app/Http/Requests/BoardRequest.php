<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BoardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'workspace_id' => 'sometimes|exists:workspaces,id',
            'name' => 'required|string|max:255',
            'position' => 'sometimes|integer',
            'color' => 'sometimes|string|max:7|regex:/^#[a-f0-9]{6}$/i',
            'is_favorite' => 'sometimes|boolean'
        ];
    }
}

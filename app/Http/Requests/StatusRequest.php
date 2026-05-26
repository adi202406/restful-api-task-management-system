<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'board_id' => 'required|exists:boards,id',
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7|regex:/^#[a-f0-9]{6}$/i',
            'position' => 'sometimes|integer',
        ];
    }
}

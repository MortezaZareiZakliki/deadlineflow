<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\SanitizesInput;

class StoreProjectRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority'    => ['required', 'in:low,medium,high'],
            'start_date'    => ['nullable', 'date'],
            'deadline'    => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}

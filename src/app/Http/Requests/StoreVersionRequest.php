<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\SanitizesInput;

class StoreVersionRequest extends FormRequest
{    
    use SanitizesInput;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'version_number' => ['required', 'string', 'max:50', 'regex:/^\d+\.\d+\.\d+$/'],
            'description'    => ['nullable', 'string'],
            'release_date'   => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'version_number.regex' => 'فرمت نسخه باید به صورت x.y.z باشد (مثلاً 1.0.0).',
        ];
    }
}
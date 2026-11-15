<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Mews\Purifier\Facades\Purifier;

trait SanitizesInput
{
    protected function prepareForValidation(): void
    {
        $data = $this->all();
        $rules = $this->rules();

        foreach ($data as $key => $value) {
            if (!isset($rules[$key])) {
                continue;
            }

            if (!is_string($value)) {
                continue;
            }

            $fieldRules = $rules[$key];
            
            if (is_string($fieldRules)) {
                $fieldRules = explode('|', $fieldRules);
            }

            $isStringField = false;
            foreach ($fieldRules as $rule) {
                if (is_string($rule) && str_contains($rule, 'string')) {
                    $isStringField = true;
                    break;
                }
                if (is_object($rule)) {
                    $isStringField = true;
                    break;
                }
            }

            if ($isStringField) {
                $data[$key] = Purifier::clean($value);
            }
        }

        $this->merge($data);
    }
}
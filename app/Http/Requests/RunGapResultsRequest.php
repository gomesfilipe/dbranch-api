<?php

namespace App\Http\Requests;

use App\Enums\Algorithm;
use App\Enums\InstanceType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RunGapResultsRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'instance_type' => ['required', Rule::enum(InstanceType::class)],
            'algorithms' => ['sometimes', 'nullable', 'array'],
            'algorithms.*' => ['sometimes', 'nullable', Rule::notIn(Algorithm::referenceAlgorithmsValues())],
        ];
    }
}

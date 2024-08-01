<?php

namespace App\Http\Requests;

use App\Enums\Algorithm;
use App\Enums\InstanceGroup;
use App\Enums\InstanceType;
use App\Enums\ValuesFromAlgorithmsMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RunValuesFromAlgorithmsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'instance_group' => ['required', Rule::enum(InstanceGroup::class)],
            'algorithms' => ['required', 'array'],
            'algorithms.*.algorithm' => ['required_with:algorithms.*.hyperparameters', Rule::enum(Algorithm::class)],
            'algorithms.*.hyperparameters' => ['required_with:algorithms.*.algorithm', 'json'],
            'd' => ['sometimes', 'nullable', 'integer', 'min:2'],
            'instance_type' => ['sometimes', 'nullable', Rule::enum(InstanceType::class)],
            'mode' => ['sometimes', 'nullable', Rule::enum(ValuesFromAlgorithmsMode::class)],
        ];
    }
}

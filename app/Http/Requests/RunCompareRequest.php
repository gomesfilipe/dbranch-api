<?php

namespace App\Http\Requests;

use App\Enums\Algorithm;
use App\Enums\InstanceGroup;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RunCompareRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'algorithm_a' => ['required', Rule::enum(Algorithm::class)],
            'algorithm_b' => ['required', Rule::enum(Algorithm::class)],
            'instance_group' => ['sometimes', 'nullable', Rule::enum(InstanceGroup::class)],
        ];
    }
}

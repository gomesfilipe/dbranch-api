<?php

namespace App\Http\Requests;

use App\Enums\Algorithm;
use App\Enums\InstanceGroup;
use App\Enums\InstanceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RunAccuracyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'instance_type' => ['required', Rule::enum(InstanceType::class)],
            'instance_group' => ['required', Rule::enum(InstanceGroup::class)],
            'algorithms' => ['sometimes', 'nullable', 'array'],
            'algorithms.*' => ['sometimes', 'nullable', Rule::in(Algorithm::hasBranchVerticesSaved())],
        ];
    }
}

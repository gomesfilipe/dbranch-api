<?php

namespace App\Http\Requests;

use App\Enums\Algorithm;
use App\Enums\InstanceGroup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RunDistancesFromOptimalRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'instance_group' => ['required', Rule::enum(InstanceGroup::class)],
            'algorithm' => ['required', Rule::enum(Algorithm::class)],
            'hyperparameters' => ['required', 'json'],
            'd' => ['sometimes', 'nullable', 'integer', 'min:2'],
        ];
    }
}

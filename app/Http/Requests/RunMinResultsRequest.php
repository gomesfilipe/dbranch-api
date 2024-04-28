<?php

namespace App\Http\Requests;

use App\Enums\InstanceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RunMinResultsRequest extends FormRequest
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
        ];
    }
}

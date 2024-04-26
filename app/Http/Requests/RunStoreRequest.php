<?php

namespace App\Http\Requests;

use App\Enums\Algorithm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class RunStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'uuid' => ['sometimes', 'uuid'],
            'vertices' => ['required', 'integer', 'min:1'],
            'edges' => ['required', 'integer', 'min:1'],
            'instance' => ['required', 'string'],
            'value' => ['required', 'integer', 'min:0'],
            'algorithm' => ['required', Rule::enum(Algorithm::class)],
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Enums\Algorithm;
use App\Rules\BranchVerticesRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RunStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            '*.vertices' => ['required', 'integer', 'min:1'],
            '*.edges' => ['required', 'integer', 'min:1'],
            '*.instance' => ['required', 'string'],
            '*.value' => ['required', 'integer', 'min:0'],
            '*.algorithm' => ['required', Rule::enum(Algorithm::class)],
            '*.time' => ['sometimes', 'nullable', 'numeric'],
            '*.branch_vertices' => ['sometimes', 'nullable', 'array'],
        ];

        foreach ($this->input('*') as $index => $item) {
            $rules["$index.branch_vertices"] = [new BranchVerticesRule($item['value'])];
        }

        return $rules;
    }

    /**
     * @throws ValidationException
     */
    public function validated($key = null, $default = null): array
    {
        $data = $this->validator->validated();

        foreach ($data as &$item) {
            $item['branch_vertices'] = json_encode($item['branch_vertices']);
        }

        return $data;
    }
}

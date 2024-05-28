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
            '*.algorithm' => ['required', Rule::enum(Algorithm::class)],
            '*.time' => ['sometimes', 'nullable', 'numeric'],
        ];

        foreach ($this->input('*') as $index => $item) {
            $rules["$index.value"] = ['required', 'integer', 'min:0', "max:{$item['vertices']}"];
            $rules["$index.branch_vertices"] = ['sometimes', 'nullable', new BranchVerticesRule($item['value'], $item['vertices'])];
        }

        return $rules;
    }
}

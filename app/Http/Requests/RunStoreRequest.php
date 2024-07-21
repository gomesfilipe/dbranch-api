<?php

namespace App\Http\Requests;

use App\Enums\Algorithm;
use App\Enums\InstanceGroup;
use App\Rules\BranchVerticesRule;
use Closure;
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
        $rules = [
            '*.vertices' => ['required', 'integer', 'min:1'],
            '*.edges' => ['required', 'integer', 'min:1'],
            '*.instance' => ['required', 'string'],
            '*.instance_group' => ['required', Rule::enum(InstanceGroup::class)],
            '*.algorithm' => ['required', Rule::enum(Algorithm::class)],
            '*.time' => ['sometimes', 'nullable', 'numeric'],
            '*.hyperparameters' => ['required', 'json'],
        ];

        foreach ($this->input('*') as $index => $item) {
            $rules["$index.value"] = ['required', 'integer', 'min:0', "max:{$item['vertices']}"];
            $rules["$index.branch_vertices"] = ['sometimes', 'nullable', new BranchVerticesRule($item['value'], $item['vertices'])];

            // Passar somente quando o algoritmo for uma meta-heurística.
            // Caso contrário, o valor desse campo deve ser obrigatoriamente nulo.
            $rules["$index.constructive_algorithm"] = [
                'present',
                'nullable',
                Rule::in(array_column(Algorithm::constructiveAlgorithms(), 'value')),
                Rule::requiredIf(fn () => $this->isMetaHeuristic($this->input("$index.algorithm"))),
                function (string $attribute, mixed $value, Closure $fail) use ($index)
                {
                    if (! $this->isMetaHeuristic($this->input("$index.algorithm")) && ! is_null($value)) {
                        $fail("The $attribute field must be null.");
                    }
                },
            ];

            // Passar somente quando o algoritmo NÃO for construtivo.
            // Caso contrário, o valor desse campo deve ser obrigatoriamente nulo.
            $rules["$index.initial_value"] = [
                'present',
                'nullable',
                'integer',
                'min:0',
                "max:{$item['vertices']}",
                Rule::requiredIf(fn () => ! $this->isConstructive($this->input("$index.algorithm"))),
                function (string $attribute, mixed $value, Closure $fail) use ($index)
                {
                    if ($this->isConstructive($this->input("$index.algorithm")) && ! is_null($value)) {
                        $fail("The $attribute field must be null.");
                    }
                },
            ];

            // Passar somente quando o algoritmo NÃO for construtivo.
            // Caso contrário, o valor desse campo deve ser obrigatoriamente nulo.
            $rules["$index.iterations"] = [
                'present',
                'nullable',
                'integer',
                'min:0',
                Rule::requiredIf(fn () => ! $this->isConstructive($this->input("$index.algorithm"))),
                function (string $attribute, mixed $value, Closure $fail) use ($index)
                {
                    if ($this->isConstructive($this->input("$index.algorithm")) && ! is_null($value)) {
                        $fail("The $attribute field must be null.");
                    }
                },
            ];
        }

        return $rules;
    }

    private function isMetaHeuristic(string $algorithm): bool
    {
        return in_array(
            $algorithm,
            array_column(Algorithm::metaHeuristicAlgorithms(), 'value'),
        );
    }

    private function isConstructive(string $algorithm): bool
    {
        return in_array(
            $algorithm,
            array_column(Algorithm::constructiveAlgorithms(), 'value'),
        );
    }
}

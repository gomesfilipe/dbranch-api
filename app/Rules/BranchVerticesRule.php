<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BranchVerticesRule implements ValidationRule
{
    public function __construct(
        private readonly int $branchVertices,
        private readonly int $vertices,
    )
    {
        //
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail("The $attribute field must be an string.");
            return;
        }

        $valueToArray = json_decode($value);

        if (! is_array($valueToArray)) {
            $fail("The $attribute field needs to be an string with array syntax.");
            return;
        }

        if (count($valueToArray) != $this->branchVertices) {
            $fail("The $attribute field needs to be exacly {$this->branchVertices} integers.");
            return;
        }

        foreach ($valueToArray as $vertice) {
            if (! is_int($vertice)) {
                $fail("The $attribute field needs to be an array of integers.");
                return;
            }

            if ($vertice < 0 || $vertice >= $this->vertices) {
                $fail("The vertices in $attribute array needs to be in range [0, {$this->vertices}).");
                return;
            }
        }
    }
}

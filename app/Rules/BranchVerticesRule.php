<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BranchVerticesRule implements ValidationRule
{
    public function __construct(private readonly int $branchVertices)
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
        if (count($value) != $this->branchVertices) {
            $fail("The $attribute field needs to be exacly {$this->branchVertices} integers.");
        }

        foreach ($value as $vertice) {
            if (! is_int($vertice)) {
                $fail("The $attribute field needs to be an array of integers.");
            }

            if ($vertice < 0 || $vertice >= $this->branchVertices) {
                $fail("The vertices in $attribute array needs to be in range [0, {$this->branchVertices}).");
            }
        }
    }
}

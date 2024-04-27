<?php

namespace App\Repositories\Interfaces;

interface OptimalRepositoryInterface
{
    public function createMany(array $data): bool;
}

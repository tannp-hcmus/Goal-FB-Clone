<?php

namespace App\Domain\Interfaces\Services;

interface ElasticSearchServiceInterface
{
    public function index(): void;
}
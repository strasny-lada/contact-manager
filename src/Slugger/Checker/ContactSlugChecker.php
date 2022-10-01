<?php declare(strict_types = 1);

namespace App\Slugger\Checker;

interface ContactSlugChecker
{

    public function isValid(string $slug): bool;

}

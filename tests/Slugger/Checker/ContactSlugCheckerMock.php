<?php declare(strict_types = 1);

namespace App\Slugger\Checker;

use Consistence\Type\ArrayType\ArrayType;

final class ContactSlugCheckerMock implements ContactSlugChecker
{

    /**
     * @param string[] $storedSlugs
     */
    public function __construct(
        private readonly array $storedSlugs,
    )
    {
    }

    public function isValid(string $slug): bool
    {
        return !ArrayType::containsValue($this->storedSlugs, $slug);
    }

}

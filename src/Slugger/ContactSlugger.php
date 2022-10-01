<?php declare(strict_types = 1);

namespace App\Slugger;

use App\Slugger\Checker\ContactSlugChecker;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ContactSlugger
{

    public function __construct(
        private readonly SluggerInterface $asciiSlugger,
        private readonly ContactSlugChecker $contactSlugChecker,
    )
    {
    }

    public function slugify(string $stringToSlugify): string
    {
        $suffixNumber = 0;

        do {
            ++$suffixNumber;

            $slug = $this->asciiSlugger->slug($stringToSlugify)
                ->lower()
                ->toString();

            if ($suffixNumber > 1) {
                $slug .= '-' . $suffixNumber;
            }
        } while (!$this->contactSlugChecker->isValid($slug));

        return $slug;
    }

}

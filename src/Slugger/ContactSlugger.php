<?php declare(strict_types = 1);

namespace App\Slugger;

use App\Slugger\Checker\ContactSlugChecker;
use Symfony\Component\String\Slugger\SluggerInterface;

// phpcs:disable PSR1.Files.SideEffects
final readonly class ContactSlugger
{

    public function __construct(
        private SluggerInterface $asciiSlugger,
        private ContactSlugChecker $contactSlugChecker,
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

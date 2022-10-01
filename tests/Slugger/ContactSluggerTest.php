<?php declare(strict_types = 1);

namespace App\Slugger;

use App\Slugger\Checker\ContactSlugCheckerMock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ContactSluggerTest extends TestCase
{

    public function testSlugCanBeGenerated(): void
    {
        $contactSlugger = new ContactSlugger(
            new AsciiSlugger(),
            new ContactSlugCheckerMock([]),
        );

        $slug = $contactSlugger->slugify('Pumpička Maxmilián');

        self::assertSame('pumpicka-maxmilian', $slug);
    }

    public function testSlugWithSuffixCanBeGenerated(): void
    {
        $contactSlugger = new ContactSlugger(
            new AsciiSlugger(),
            new ContactSlugCheckerMock([
                'ventil-hugo',
                'ventil-hugo-2',
            ]),
        );

        $slug = $contactSlugger->slugify('Ventil Hugo');

        self::assertSame('ventil-hugo-3', $slug);
    }

}

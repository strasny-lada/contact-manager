<?php declare(strict_types = 1);

namespace App\Slugger\Checker;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;

// phpcs:disable PSR1.Files.SideEffects
final readonly class ContactSlugCheckerORM implements ContactSlugChecker
{

    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function isValid(string $slug): bool
    {
        return $this->entityManager->getRepository(Contact::class)->count([
            'slug' => $slug,
        ]) === 0;
    }

}

<?php declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;

final class ContactRepository
{

    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return \App\Entity\Contact[]
     */
    public function fetchAll(): array
    {
        return $this
            ->entityManager
            ->createQueryBuilder()
            ->select([
                'contact',
            ])
            ->from(Contact::class, 'contact')
            ->addOrderBy('contact.lastname', 'ASC')
            ->addOrderBy('contact.firstname', 'ASC')
            ->getQuery()
            ->getResult();
    }

}

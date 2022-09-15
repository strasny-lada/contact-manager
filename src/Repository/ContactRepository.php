<?php declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Contact;
use App\Entity\ContactStatus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

final class ContactRepository
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function getFetchAllQuery(): Query
    {
        return $this
            ->entityManager
            ->createQueryBuilder()
            ->select([
                'contact',
            ])
            ->from(Contact::class, 'contact')
            ->andWhere('contact.status = :activeStatus')->setParameter('activeStatus', ContactStatus::ACTIVE)
            ->addOrderBy('contact.lastname', 'ASC')
            ->addOrderBy('contact.firstname', 'ASC')
            ->addOrderBy('contact.slug', 'ASC')
            ->getQuery();
    }

}

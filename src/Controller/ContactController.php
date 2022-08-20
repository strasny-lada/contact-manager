<?php declare(strict_types = 1);

namespace App\Controller;

use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="contact_")
 */
final class ContactController extends AbstractController
{

    /**
     * @Route("", methods={"GET"}, name="list")
     */
    public function index(
        ContactRepository $contactRepository
    ): Response
    {
        return $this->render('contact/list.html.twig', [
            'contacts' => $contactRepository->fetchAll(),
        ]);
    }

}

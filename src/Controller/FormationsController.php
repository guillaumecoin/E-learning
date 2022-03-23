<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormationsController extends AbstractController
{
    /**
     * @var FormationRepository
     */
    private $formationRepository;

    public function __construct(FormationRepository $formationRepository)
    {
        $this->formationRepository = $formationRepository;
    }

    /**
     * @Route("/formations", name="app_formations")
     */
    public function index(Request $request): Response
    {
        return $this->renderForm('formations/index.html.twig', [
            'formations' => $this->formationRepository->findAll(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Formation;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModificationFormationController extends AbstractController
{
    /**
     * @Route("/gestion/formation/modifier/{formationId}", name="modifier")
     */
    public function index(): Response
    {
        return $this->render('modification_formation/index.html.twig', [
            'controller_name' => 'ModificationFormationController',
        ]);
    }

    /**
     * @Route("/gestion/formation/supprimer/{formationId}", name="supprimer")
     * @param $formationId
     * @return Response
     */
    public function supprimer($formationId, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $formation = $entityManager->getRepository(Formation::class)->find($formationId);
        $entityManager->remove($formation);
        $entityManager->flush();

        return $this->redirectToRoute('app_gestion_formation');
    }
}

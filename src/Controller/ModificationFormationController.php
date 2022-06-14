<?php

namespace App\Controller;


use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ModificationFormationController extends AbstractController
{
    /**
     * @var FormationRepository
     */
    private FormationRepository $formationRepository;

    public function __construct(FormationRepository $formationRepository)
    {
        $this->formationRepository = $formationRepository;
    }


    /**
     * @Route("/gestion/formation/modifier/{formationId}", name="modifier")
     */
    public function modifier(int $formationId, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $formation = $this->formationRepository->find($formationId);
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $PdfFichier */
            $PdfFichier = $form->get('url')->getData();


            if ($PdfFichier) {
                $FichierDeBase = pathinfo($PdfFichier->getClientOriginalName(), PATHINFO_FILENAME);
                $securisation = $slugger->slug($FichierDeBase);
                $nouveauFichier = $securisation . '-' . uniqid() . '.' . $PdfFichier->guessExtension();

                try {
                    $PdfFichier->move(
                        $this->getParameter('fichier'),
                        $nouveauFichier
                    );
                } catch (FileException $e) {

                }

                $formation->setURL($nouveauFichier);



            }


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($form->getData());
            $entityManager->flush();

            return $this->redirectToRoute('app_gestion_formation');

        }

        return  $this->render('modification_formation/index.html.twig',[
            'form' => $form->createView()

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

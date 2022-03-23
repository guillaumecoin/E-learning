<?php

namespace App\Controller;

use App\Entity\Formation;

use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class GestionFormationController extends AbstractController
{
    private  $formationRepository;

    public function __construct(FormationRepository $formationRepository){
        $this->formationRepository = $formationRepository;
    }
    /**
     * @Route("/gestion/formation", name="app_gestion_formation")
     */


    public function index(Request $request, SluggerInterface $slugger): Response
    {

        $formation = new Formation();
        $form = $this->createForm(FormationType::class,  $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            /** @var UploadedFile $PdfFichier */
            $PdfFichier = $form->get('url')->getData();


            if ($PdfFichier){
                $FichierDeBase = pathinfo($PdfFichier->getClientOriginalName(), PATHINFO_FILENAME);
                $securisation = $slugger->slug($FichierDeBase);
                $nouveauFichier = $securisation.'-'.uniqid().'.'.$PdfFichier->guessExtension();

                try {
                    $PdfFichier->move(
                        $this->getParameter('fichier'),
                        $nouveauFichier
                    );
                } catch (FileException $e){

                }
                $formation->setURL($nouveauFichier);

            }



            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($form->getData());
            $entityManager->flush();

            return $this->redirectToRoute('app_gestion_formation');
        }
        return $this->render('gestion_formation/index.html.twig', [
            'controller_name' => 'GestionFormationController',
            'formations' => $this->formationRepository->findAll(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/formation/{formationId}", name="formation")
     * * @param $formationId
     */
    public function modifier($formationId): Response
    {
        return $this->render('presentation/index.html.twig', [
            'controller_name' => 'PresentationController',
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

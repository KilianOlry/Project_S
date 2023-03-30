<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Form\Contrat1Type;
use App\Repository\ContratRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('/souscritpion')]
class SouscritpionController extends AbstractController
{
    #[Route('/', name: 'app_souscritpion_index', methods: ['GET'])]
    public function index(ContratRepository $contratRepository): Response
    {
        return $this->render('souscritpion/index.html.twig', [
            'contrats' => $contratRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_souscritpion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContratRepository $contratRepository, SluggerInterface $slugger): Response
    {
        $contrat = new Contrat();
        $form = $this->createForm(Contrat1Type::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('reservisteFiles')->getData();

            //renommage du fichier
            if ($image) {
                $originaleFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originaleFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('ContratuserImage'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $contrat->setReservisteFiles($newFilename);
            }
            $contratRepository->save($contrat, true);

        }

        return $this->renderForm('souscritpion/new.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }
}

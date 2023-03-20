<?php

namespace App\Controller;

use App\Entity\OffreAssurance;
use App\Form\OffreAssuranceType;
use App\Repository\OffreAssuranceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin/offre/assurance')]
class AdminOffreAssuranceController extends AbstractController
{
    #[Route('/', name: 'app_admin_offre_assurance_index', methods: ['GET'])]
    public function index(OffreAssuranceRepository $offreAssuranceRepository): Response
    {
        return $this->render('admin_offre_assurance/index.html.twig', [
            'offre_assurances' => $offreAssuranceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_offre_assurance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OffreAssuranceRepository $offreAssuranceRepository, SluggerInterface $slugger): Response
    {
        $offreAssurance = new OffreAssurance();
        $form = $this->createForm(OffreAssuranceType::class, $offreAssurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('fichier')->getData();

            //renommage du fichier
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('offreAssuranceImage'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $offreAssurance->setFichier($newFilename);
            }
            $offreAssuranceRepository->save($offreAssurance, true);

            return $this->redirectToRoute('app_admin_offre_assurance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_offre_assurance/new.html.twig', [
            'offre_assurance' => $offreAssurance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_offre_assurance_show', methods: ['GET'])]
    public function show(OffreAssurance $offreAssurance): Response
    {
        return $this->render('admin_offre_assurance/show.html.twig', [
            'offre_assurance' => $offreAssurance,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_offre_assurance_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OffreAssurance $offreAssurance, OffreAssuranceRepository $offreAssuranceRepository): Response
    {
        $form = $this->createForm(OffreAssuranceType::class, $offreAssurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offreAssuranceRepository->save($offreAssurance, true);

            return $this->redirectToRoute('app_admin_offre_assurance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_offre_assurance/edit.html.twig', [
            'offre_assurance' => $offreAssurance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_offre_assurance_delete', methods: ['POST'])]
    public function delete(Request $request, OffreAssurance $offreAssurance, OffreAssuranceRepository $offreAssuranceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offreAssurance->getId(), $request->request->get('_token'))) {
            $offreAssuranceRepository->remove($offreAssurance, true);
        }

        return $this->redirectToRoute('app_admin_offre_assurance_index', [], Response::HTTP_SEE_OTHER);
    }
}

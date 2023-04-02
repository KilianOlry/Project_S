<?php

namespace App\Controller;

use Dompdf\Dompdf;
use App\Entity\Contrat;
use App\Form\ContratType;
use App\Service\YouSignService;
use App\Repository\ContratRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin/souscription')]
class AdminSouscriptionController extends AbstractController
{
    #[Route('/', name: 'app_souscription_index', methods: ['GET'])]
    public function index(ContratRepository $contratRepository): Response
    {
        return $this->render('admin-souscription/index.html.twig', [
            'contrats' => $contratRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_souscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContratRepository $contratRepository, SluggerInterface $slugger): Response
    {
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('reservisteFiles')->getData();

            //renommage du fichier
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('identityUser'),
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

            return $this->redirectToRoute('app_souscription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin-souscription/new.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_souscription_show', methods: ['GET'])]
    public function show(Contrat $contrat): Response
    {
        return $this->render('admin-souscription/show.html.twig', [
            'contrat' => $contrat,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_souscription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contrat $contrat, ContratRepository $contratRepository): Response
    {
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contratRepository->save($contrat, true);

            return $this->redirectToRoute('app_souscription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin-souscription/edit.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_souscription_delete', methods: ['POST'])]
    public function delete(Request $request, Contrat $contrat, ContratRepository $contratRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contrat->getId(), $request->request->get('_token'))) {
            $contratRepository->remove($contrat, true);
        }

        return $this->redirectToRoute('app_souscription_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/pdf', name: 'app_souscription_pdf', methods: ['GET'])]
    public function pdf(Request $request, Contrat $contrat, ContratRepository $contratRepository): Response
    {
        // Instanciation d'un nouvel objet dompdf
        $dompdf = new Dompdf();

        // On stock le template du fichier pdf dans la variable 'html'
        $html = $this->renderView('admin-souscription/pdf.html.twig', [
            'contrat' => $contrat
        ]);
        // on transfert le rendu pdf à l'objet dompdf
        $dompdf->loadHtml($html);

        // on choisit le format et l'orientation du fichier
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();
        // on demande qu'il récupère le fichier pdf grâce à la méthode output
        $output = $dompdf->output();

        // On construit à nom de fichier qui sera "contrat_id.pdf"
        $filename = 'contrat_' . $contrat->getId() . '.pdf';

        // On définit le chemin dans lequel le fichier sera stocker dans le projet
        $file = $this->getParameter('kernel.project_dir') . '/public/assets/uploads/contrats/' . $filename;

        // On renseigne le champ contrat dans l'entité
        $contrat->setPdfNoFirm($filename);

        // On le fait persister
        $contratRepository->save($contrat, true);

        // On enregistre le ficher
        file_put_contents($file, $output);

        return $this->redirectToRoute('app_souscription_show', ['id' => $contrat->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/signature', name: 'app_souscription_signature', methods: ['GET'])]
    public function signature(Contrat $contrat, ContratRepository $contratRepository, YouSignService $youSignService): Response
    {
        //01 création de la demande de signature
        $yousignSignatureRequest = $youSignService->signatureRequest();
        $contrat->setSignatureId($yousignSignatureRequest['id']);
        $contratRepository->save($contrat, true);

        //02 Upload le document
        $uploadDocument = $youSignService->uploadDocument($contrat->getSignatureId(), $contrat->getPdfNoFirm());
        $contrat->setDocumentId($uploadDocument['id']);
        $contratRepository->save($contrat, true);

        //03 ajout des signataires
        $signerId = $youSignService->addSigner(
            $contrat->getSignatureId(),
            $contrat->getDocumentId(),
            $contrat->getEmail(),
            $contrat->getPrenom(),
            $contrat->getNom()
        );
        $contrat->setSignerId($signerId['id']);
        $contratRepository->save($contrat, true);

        //04 envoie de la demande de signature
        $youSignService->activateSignatureRequest($contrat->getSignatureId());

        return $this->redirectToRoute('app_souscription_show', ['id' => $contrat->getId()], Response::HTTP_SEE_OTHER);
    }
}

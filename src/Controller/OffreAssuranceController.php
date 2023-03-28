<?php

namespace App\Controller;

use App\Entity\OffreAssurance;
use App\Form\OffreAssurance1Type;
use App\Repository\OffreAssuranceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/offre/assurance')]
class OffreAssuranceController extends AbstractController
{
    #[Route('/', name: 'app_Offre_assurance_index', methods: ['GET'])]
    public function index(OffreAssuranceRepository $offreAssuranceRepository): Response
    {

        // méthode findall sert à faire la liste de chaque donnée de cette colone dans le fichier twig correspondant
        return $this->render('Offre_assurance/index.html.twig', [
            'offre_assurances' => $offreAssuranceRepository->findAll(),
        ]);
    }

    #[Route('/premium-reserviste', name: 'app_info_global')]
    public function mentionlegales(): Response
    {
        return $this->render('Info-global/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }


    #[Route('/premium-reserviste/detail', name: 'app_info_detail')]
    public function details(): Response
    {
        return $this->render('InfoDetail/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}

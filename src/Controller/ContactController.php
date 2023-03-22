<?php

// Pour l'envoi d'email il faut 3 étapes récupérer les données du formulaire / créer le mail / envoyer le mail

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;



class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]

    // l'objet resquest de http foundation récupère les donnée dans la requête http du navigateur

    public function index(Request $request, MailerInterface $mailer): Response
    {

        // Création du formulaire Contact qu'on place dans une variable pour l'afficher après

        $form = $this->createForm(ContactType::class);

        // Récupération des data dans le formulaire

        $form->handleRequest($request);

        // Récupération des data seulement si le formulaire à été submit + data cohérentes

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupération des données et tockage dans variables différentes
            $address = $form->get('email')->getData();
            $objet = $form->get('objet')->getData();
            $message = $form->get('message')->getData();




            // Création de l'email contenant les data du formulaire

            $email = (new Email())
                ->from($address)
                ->to('admin.admin@gmail.com')
                ->subject($objet)
                ->text($message);


            // dd($address, $objet, $message);

            $mailer->send($email);
        }

        // On renvoie ce visuel là quand on est sur la page

        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',

            //On affiche le formulaire dans le template twig on le place dans la variable twig 'form'

            'form' => $form

        ]);
    }
}

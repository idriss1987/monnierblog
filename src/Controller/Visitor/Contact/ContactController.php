<?php

namespace App\Controller\Visitor\Contact;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Service\SendEmailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'visitor.contact.create')]
    public function create(Request $request , ContactRepository $contactRepository , SendEmailService $sendEmailService): Response
    {   
        $contact = new Contact();
        $form = $this->createForm(ContactType::class,$contact);

        $form->handleRequest( $request);

        if ($form->isSubmitted() && $form -> isValid()) 
        {
            $contactRepository->save($contact,true);
            //envoi d'email
            $sendEmailService->send([
                "sender_email"          => $contact->getEmail(),
                "sender_name"           => $contact->getFirstName() . " " . $contact->getLastName(),
                "recipient_email"       => "pascal-monnier@gmail.com",
                "subject"               => "Reception d'un message sur votre blog",
                "html_template"         => "email/contact.html.twig",
                "context"               => [
                    "first_name" => $contact->getFirstName(),
                    "last_name"  => $contact->getLastName(),
                    "message"   => $contact->getMessage(),
                    ]
            ]);


            $this->addFlash("success","Votre message a été envoyé ! Nous vous répondrons par email dans les plus brefs délais.");

            return $this ->redirectToRoute("visitor.contact.create");
        }

        return $this->renderForm('pages/visitor/contact/create.html.twig' , compact('form'));
    }
}

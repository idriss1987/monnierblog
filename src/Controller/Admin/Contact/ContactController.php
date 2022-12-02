<?php

namespace App\Controller\Admin\Contact;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
class ContactController extends AbstractController
{
    #[Route('/admin/contact/list', name: 'admin.contact.index')]
    public function index( ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository ->findAll();
    
        return $this->render('pages/admin/contact/index.html.twig', compact('contacts'));
    }

    #[Route('/admin/contact/delete/{id<\d+>}', name: 'admin.contact.delete')]
    public function delete(Contact $contact, Request $request,  ContactRepository $contactRepository) : Response
    {
        if ( $this->isCsrfTokenValid("delete_contact_".$contact->getId(), $request->request->get('_csrf_token')) ) 
        {
            $contactRepository->remove($contact, true);
            $this->addFlash("success", "Ce message a été supprimé!");
        }
        
        return $this->redirectToRoute('admin.contact.index');
    }

    #[Route('/admin/contact/{id<\d+>}/show', name: 'admin.contact.show')]
    public function show(Contact $contact) : Response
    {
       
        return $this->render('pages/admin/contact/show.html.twig',compact('contact'));
    }


}

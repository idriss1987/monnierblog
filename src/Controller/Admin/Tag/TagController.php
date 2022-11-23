<?php

namespace App\Controller\Admin\Tag;

use App\Entity\Tag;
use App\Form\TagFormType;
use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
class TagController extends AbstractController
{
    #[Route('/admin/tag/list', name: 'admin.tag.index')]
    public function index(TagRepository $tagRepository): Response
    {
        $tags =$tagRepository->findAll();
        return $this->render('pages/admin/tag/index.html.twig', compact('tags'));
    }


    #[Route('/admin/tag/create', name: 'admin.tag.create')]
    public function create(Request $request , TagRepository $tagRepository) : Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagFormType::class, $tag);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) 
        {
            $tagRepository->save($tag, true);

            $this->addFlash("success", "Votre tag a été créée avec succès!");

            return $this->redirectToRoute('admin.tag.index');
        }


        return $this->renderForm("pages/admin/tag/create.html.twig", compact('form'));

    }

    #[Route('/admin/tag/{id<\d+>}/edit', name: 'admin.tag.edit')]
    public function edit(Tag $tag, Request $request, TagRepository $tagRepository) : Response
    {
        $form = $this->createForm(TagFormType::class, $tag);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() )
        {
            $tagRepository->save($tag, true);
            $this->addFlash('success', "Ce tag a été modifié avec succès!");
            return $this->redirectToRoute('admin.tag.index');
        }

        return $this->renderForm("pages/admin/tag/edit.html.twig", compact('form'));
        // return $this->render("pages/admin/tag/edit.html.twig", array('form' => $form->createView()));
    }

    #[Route('/admin/tag/{id<\d+>}/delete', name: 'admin.tag.delete')]
    public function delete(Tag $tag, Request $request,  TagRepository $tagRepository) : Response
    {
        if ( $this->isCsrfTokenValid("delete_tag_".$tag->getId(), $request->request->get('_csrf_token')) ) 
        {
            $tagRepository->remove($tag, true);
            $this->addFlash("success", "Ce tag a été supprimé!");
        }
        
        return $this->redirectToRoute('admin.tag.index');
    }



}

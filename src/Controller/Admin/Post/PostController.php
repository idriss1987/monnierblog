<?php

namespace App\Controller\Admin\Post;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
class PostController extends AbstractController
{


    #[Route('/admin/post/list-posts', name: 'admin.post.index' , methods:['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        return $this->render('pages/admin/post/index.html.twig', compact('posts'));
    }


    #[Route('/admin/post/create', name: 'admin.post.create', methods:['GET','POST'])]
    public function create(Request $request, PostRepository $postRepository , CategoryRepository $categoryRepository): Response
    {

        if (! $categoryRepository->findAll()) {
            $this->addFlash("warning", "vous devez créer au moins une catégorie avant de créér des articles");
            return $this->redirectToRoute("admin.category.index");
        }


        $post = new Post();

        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) 
        {
            $post->setUser($this->getUser());
            $postRepository->save($post, true);

            $this->addFlash("success", "L'article a bien été ajouté!");
            return $this->redirectToRoute("admin.post.index");
        }

        return $this->renderForm('pages/admin/post/create.html.twig', compact('form'));
    }


    #[Route('/admin/post/{id<\d+>}/published', name: 'admin.post.published' , methods:['POST'])]
    public function published(Post $post, PostRepository $postRepository) : Response
    {
        if ($post->isIsPublished()=== false) 
        {
            $post->setIsPublished(true);
            $post->setPublishedAt(new \DateTimeImmutable('now'));
            $postRepository ->save($post,true);
            $this->addFlash("success", " Votre article a été publié ");
        }
        else
        {
            $post->setIsPublished(false);
            $post->setPublishedAt(null);
            $postRepository ->save($post,true);
            $this->addFlash("success", " Votre article a été rétiré de la liste des publications ");
        }
        
        return $this->redirectToRoute("admin.post.index");
    }


    #[Route('/admin/post/{id<\d+>}/show', name: 'admin.post.show' , methods:['GET'])]
    public function show(Post $post , Request $request) : Response
    {
        return $this->render("pages/admin/post/show.html.twig",compact('post'));
    
    }

    #[Route('/admin/post/{id<\d+>}/edit', name: 'admin.post.edit' , methods:['GET','POST'])]
    public function edit(Post $post, Request $request, PostRepository $postRepository)
    {
       $form= $this->createForm(PostFormType::class, $post);
       $form->handleRequest($request);

       if ( $form->isSubmitted() && $form->isValid() ) 
       {
           $post->setUser($this->getUser());
           $postRepository->save($post, true);

           $this->addFlash("success", "L'article a bien été modifié!");
           return $this->redirectToRoute("admin.post.index");
       }

       return $this->renderForm("pages/admin/post/edit.html.twig",compact('form', 'post'));
    }

    #[Route('/admin/post/{id<\d+>}/delete', name: 'admin.post.delete', methods: ['POST'])]
    public function delete(Post $post, Request $request, PostRepository $postRepository) :  Response
    {
        if ( $this->isCsrfTokenValid("delete_post_".$post->getId(), $request->request->get('_csrf_token')) ) 
        {
            $postRepository->remove($post, true);
            $this->addFlash("success", "Cet article été supprimée!");
        }
        
        return $this->redirectToRoute('admin.post.index');
    }

    
}
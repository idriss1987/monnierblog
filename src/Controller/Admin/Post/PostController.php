<?php

namespace App\Controller\Admin\Post;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
class PostController extends AbstractController
{
    #[Route('/admin/post/list-posts', name: 'admin.post.index')]
    public function index(PostRepository $postRepository): Response
    {
        $posts=$postRepository->findAll();
        return $this->render('pages/admin/post/index.html.twig', compact('posts'));
    }

    #[Route('/admin/post/create', name: 'admin.post.create')]
    public function create(Request $request , PostRepository $postRepository): Response
    {
        $post=new Post();

        $form=$this->createForm(PostFormType::class,$post);

        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()) 
        {
            $post->setUser($this->getUser());
            $postRepository->save($post,true);

            $this->addFlash("success","L'article a bien été ajouté!");
            return $this->redirectToRoute("admin.post.index");
        }


        return $this->renderForm('pages/admin/post/create.html.twig',compact('form'));
    }

}


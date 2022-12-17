<?php

namespace App\Controller\Admin\Comment;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends AbstractController
{
    #[Route('/admin/comment/list', name: 'admin.comment.index')]
    public function index(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findAll();

        

        return $this->render('pages/admin/comment/index.html.twig', compact('comments'));
    }

    #[Route('/admin/comment/{id<\d+>}/show', name: 'admin.comment.show')]
    public function show(Comment $comment): Response
    {
        
        return $this->render('pages/admin/comment/show.html.twig', compact('comment'));
    }

    #[Route('/admin/comment/{id<\d+>}/delete', name: 'admin.comment.delete')]
    public function delete(Comment $comment, Request $request , CommentRepository $commentRepository): Response
    {
        if ($this-> isCsrfTokenValid('comment_'. $comment->getId(), $request->request ->get('_csrf_token')) )
        {
          $commentRepository->remove($comment,true);
          $this->addFlash("success","Ce commentaire a été supprimé !");
        }

        return $this->redirectToRoute('admin.comment.index');
    }

}

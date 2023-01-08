<?php

namespace App\Controller\Admin\Home;

use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use App\Repository\ContactRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[IsGranted('ROLE_ADMIN')]

class HomeController extends AbstractController
{
    #[Route('/admin/home', name: 'admin.home.index')]
    public function index(
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        PostRepository $postRepository,
        UserRepository $userRepository,
        CommentRepository $commentRepository,
        ContactRepository $contactRepository
        ): Response
    {

        $categories = $categoryRepository->findAll();
        $tags       = $tagRepository->findAll();
        $posts      = $postRepository->findAll();
        $users      = $userRepository->findAll();
        $comments   = $commentRepository->findAll();
        $contacts   = $contactRepository->findAll();

        return $this->render('pages/admin/home/index.html.twig', compact('categories', 'tags', 'posts', 'users', 'comments', 'contacts'));
    }

}

<?php

namespace App\Controller\Visitor\Blog;

use App\Entity\Tag;
use App\Entity\Post;
use App\Entity\Category;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route('/blog/all-posts', name: 'visitor.blog.index')]
    public function index(CategoryRepository $categoryRepository ,PostRepository $postRepository , TagRepository $tagRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $tags = $tagRepository->findAll();
        $posts = $postRepository->findBy(['isPublished'=>true]);
        return $this->render('pages/visitor/blog/index.html.twig' , compact('categories','tags','posts'));

    }

    #[Route('/blog/{id<\d+>}/{slug}', name: 'visitor.blog.post.show')]
    public function show(Post $post) : Response
    {
        return $this->render('pages/visitor/blog/show.html.twig', compact('post'));
    }







    #[Route('/blog/{id<\d+>}/{slug}/filter-by-category', name: 'visitor.blog.post.filter_by_category')]
    public function filterByCategory(
        CategoryRepository $categoryRepository, 
        TagRepository $tagRepository,
        PostRepository $postRepository,
        Category $category
    ) : Response
    {
        
        $categories = $categoryRepository->findAll();
        $tags       = $tagRepository->findAll();
        $posts      = $postRepository->filterPostsByCategory($category->getId());

        return $this->render('pages/visitor/blog/index.html.twig', compact('categories', 'tags', 'posts'));
    }

    #[Route('/blog/{id<\d+>}/{slug}/filter-by-tag', name: 'visitor.blog.post.filter_by_tag')]
    public function filterBytag(
        CategoryRepository $categoryRepository, 
        TagRepository $tagRepository,
        PostRepository $postRepository,
        Tag $tag
    ) : Response
    {
        
        $categories = $categoryRepository->findAll();
        $tags       = $tagRepository->findAll();
        $posts      = $postRepository->filterPostsByTag($tag);

        return $this->render('pages/visitor/blog/index.html.twig', compact('categories', 'tags', 'posts'));
    }


   


}

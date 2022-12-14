<?php

namespace App\Controller\Visitor\Blog;

use App\Entity\Tag;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\PostLike;
use App\Form\CommentFormType;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Repository\CategoryRepository;
use App\Repository\PostLikeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route('/blog/all-posts', name: 'visitor.blog.index')]
    public function index(CategoryRepository $categoryRepository ,PostRepository $postRepository , TagRepository $tagRepository , PaginatorInterface $paginator , Request $request): Response
    {
        $categories = $categoryRepository->findAll();
        $tags = $tagRepository->findAll();
        $posts = $postRepository->findBy(['isPublished'=>true]);

        $pagination = $paginator->paginate(
            $posts, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
    
        return $this->render('pages/visitor/blog/index.html.twig' , compact('categories','tags','pagination'));

    }

    #[Route('/blog/{id<\d+>}/{slug}', name: 'visitor.blog.post.show')]
    public function show(Post $post , Request $request , CommentRepository $commentRepository) : Response
    {
        $comment = new Comment();
         $form= $this->createForm(CommentFormType::class , $comment);
         $post->getComments();
         $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
         {
            $comment->setPost($post);
            $comment->setUser($this->getUser());
           $commentRepository -> save($comment , true);
            return $this->redirectToRoute('visitor.blog.post.show',[
            'id'=>$post -> getId(), 
           'slug' => $post->getSlug()]);
        }


        return $this->renderForm('pages/visitor/blog/show.html.twig', compact('post' , 'form'));
    }







    #[Route('/blog/{id<\d+>}/{slug}/filter-by-category', name: 'visitor.blog.post.filter_by_category')]
    public function filterByCategory(
        CategoryRepository $categoryRepository, 
        TagRepository $tagRepository,
        PostRepository $postRepository,
        Category $category,
        PaginatorInterface $paginator,
        Request $request
    ) : Response
    {
        
        $categories = $categoryRepository->findAll();
        $tags       = $tagRepository->findAll();
        $posts      = $postRepository->filterPostsByCategory($category->getId());

        $pagination = $paginator->paginate(
            $posts, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );

        return $this->render('pages/visitor/blog/index.html.twig', compact('categories', 'tags', 'pagination'));
    }

    #[Route('/blog/{id<\d+>}/{slug}/filter-by-tag', name: 'visitor.blog.post.filter_by_tag')]
    public function filterBytag(
        CategoryRepository $categoryRepository, 
        TagRepository $tagRepository,
        PostRepository $postRepository,
        Tag $tag,
        PaginatorInterface $paginator,
        Request $request
    ) : Response
    {
        
        $categories = $categoryRepository->findAll();
        $tags       = $tagRepository->findAll();
        $posts      = $postRepository->filterPostsByTag($tag);

        $pagination = $paginator->paginate(
            $posts, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );

        return $this->render('pages/visitor/blog/index.html.twig', compact('categories', 'tags', 'pagination'));
    }

    #[Route('/blog/post/{id<\d+>}/{slug}/like', name: 'visitor.blog.post.like')]
    public function like(Post $post, PostLikeRepository $postLikeRepository) : Response
    {
        $user = $this->getUser();

        if (!$user) 
        {
            return $this->json(array('code' => 403, 'message' => 'Unautorized'), 403);
        }

        if ( $post->isLikedByUser($user) ) 
        {
            $post_liked = $postLikeRepository->findOneBy(array('post' => $post, 'user' => $user));

            $postLikeRepository->remove($post_liked, true);
            
            return $this->json(array(
                'code' => 200, 
                'message' => 'Like bien supprim??',
                'postLikes' => $postLikeRepository->count(array('post' => $post))
            ), 200);
        }

        $postLike = new PostLike();
        $postLike->setPost($post);
        $postLike->setUser($user);

        $postLikeRepository->save($postLike, true);

        return $this->json(array(
            'code' => 200, 
            'message' => 'Like bien ajout??',
            'postLikes' => $postLikeRepository->count(array('post' => $post))
        ), 200);
    }


}

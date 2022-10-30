<?php

namespace App\Controller\Admin\Category;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/admin/category/categories-list', name: 'admin.category.index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories= $categoryRepository->findAll();
        
        return $this->render('pages/admin/category/index.html.twig',compact('categories'));
        // return $this->render('pages/admin/category/index.html.twig', ['categories' => $categories]);

    }

    #[Route('/admin/category/create', name: 'admin.category.create')]
    public function create(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category=new Category();

        $form=$this->createForm(CategoryFormType::class,$category);

        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid())
        {
            $categoryRepository->save($category,true);

            $this->addFlash("success","Votre catégorie a été crée avec succès!");
            
            return $this->redirectToRoute('admin.category.index');
        }


        return $this->renderForm('pages/admin/category/create.html.twig',compact('form'));
    }
}

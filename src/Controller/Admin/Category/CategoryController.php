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

    #[Route('/admin/category/edit/{id<\d+>}', name: 'admin.category.edit')]
        public function edit(Category $category, Request $request , CategoryRepository $categoryRepository)
        {
            $form=$this->createForm(CategoryFormType::class, $category);

            $form->handleRequest($request);

            if ($form->isSubmitted()&& $form->isValid()) 
            {
                $categoryRepository->save($category, true);
                $this->addFlash('success',"cette catégorie a été modifiée avec succès!");
                return $this->redirectToRoute('admin.category.index');
            }

            return $this->renderForm("pages/admin/category/edit.html.twig",compact('form'));
        
        }

        #[Route('/admin/category/delete/{id<\d+>}', name: 'admin.category.delete')]
        public function delete(Category $category, Request $request,  CategoryRepository $categoryRepository) : Response
        {
            if ( $this->isCsrfTokenValid("delete_category_".$category->getId(), $request->request->get('_csrf_token')) ) 
            {
                $categoryRepository->remove($category, true);
                $this->addFlash("success", "Cette catégorie été supprimée!");
            }
            
            return $this->redirectToRoute('admin.category.index');
    
        }
    

}

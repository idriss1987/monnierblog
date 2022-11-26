<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use App\Form\EditRoleFormType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/admin/user/list', name: 'admin.user.index')]
    public function index(UserRepository $userRepository): Response
    {
        $users= $userRepository ->findAll();
        
        return $this->render('pages/admin/user/index.html.twig', compact('users'));
    }

    


    #[Route('/admin/user/{id<\d+>}/edit_role', name: 'admin.user.edit_role')]
    public function editRole(User $user, Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(EditRoleFormType::class,$user);

        $form -> handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid()) 
        {
            $userRepository->save($user, true);
            $this->addFlash("success", "Les rôles de " . $user->getFirstName() . " " . $user->getLastName() . " ont été modifié avec succès.");
            return $this->redirectToRoute('admin.user.index');
        }
        return $this->renderForm("pages/admin/user/edit_role.html.twig",compact('form'));

    }


    #[Route('/admin/user/{id<\d+>}/delete', name: 'admin.user.delete')]
    public function delete(User $user, Request $request, UserRepository $userRepository): Response
    {
        if ( $this->isCsrfTokenValid("delete_user_".$user->getId(), $request->request->get('_csrf_token')) ) 
        {
            $userRepository->remove($user, true);
            $this->addFlash("success", "Cet utilisateur a été supprimé!");
        }
        
        return $this->redirectToRoute('admin.user.index');
    }


    
}




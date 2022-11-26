<?php

namespace App\Controller\Admin\Profile;

use App\Entity\User;
use App\Form\EditProfileFormType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[IsGranted('ROLE_ADMIN')]
class ProfileController extends AbstractController
{


    #[Route('/admin/profile', name: 'admin.profile.index')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('pages/admin/profile/index.html.twig', compact('user'));
    }


    #[Route('/admin/profile/{id<\d+>}/edit', name: 'admin.profile.edit')]
    public function edit(
        User $user, 
        Request $request, 
        UserRepository $userRepository, 
        UserPasswordHasherInterface $hasher
    ): Response
    {
        $form = $this->createForm(EditProfileFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            // encode the plain password
            $user->setPassword($hasher->hashPassword($user,$form->get('password')->getData()));

            $userRepository->save($user, true);

            $this->addFlash("success", "Le profil a été modifié avec succès");
            return $this->redirectToRoute("admin.profile.index");
        }

        return $this->renderForm('pages/admin/profile/edit.html.twig', compact('form'));
    }
}

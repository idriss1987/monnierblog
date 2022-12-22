<?php

namespace App\Controller\User\Profile;

use App\Form\EditProfileFormType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'user.profile.index')]
    public function index(): Response
    {
        return $this->render('pages/user/profile/index.html.twig' );
    }

    #[Route('/user/profile/edit', name: 'user.profile.edit')]
    public function edit(

        Request $request, 
        UserRepository $userRepository, 
        UserPasswordHasherInterface $hasher
    ): Response
    {   
        $user= $this->getUser();
        $form = $this->createForm(EditProfileFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            // encode the plain password
            $user->setPassword($hasher->hashPassword($user,$form->get('password')->getData()));

            $userRepository->save($user, true);

            $this->addFlash("success", "Le profil a été modifié avec succès");
            return $this->redirectToRoute("user.profile.index");
        }

        return $this->render('pages/user/profile/edit.html.twig', [
            "form" =>$form->createView()
        ]);
    }
}



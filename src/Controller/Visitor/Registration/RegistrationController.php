<?php

namespace App\Controller\Visitor\Registration;



use App\Entity\User;
use App\Service\SendEmailService;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'visitor.registration.register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator, SendEmailService $sendEmailService): Response
    {
        if ($this->getUser())
         {
             return $this->redirectToRoute('visitor.welcome.index');
         }
         
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           
            // generate the token for email verification
            $tokenGenerated = $tokenGenerator->generateToken();
            $user->setTokenForEmailVerification($tokenGenerated);

            //  generate the dead line for mail verification
            $deadline = (new \DateTimeImmutable('now'))->add(new \DateInterval('P1D'));
            $user->setTokenForEmailVerificationExpiresAt($deadline);

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            //  send the registation email
            $sendEmailService->send([
                "sender_email" => "pascal-monnier@gmail.com",
                "sender_name" => "Pascal Monnier",
                "recipient_email" => $user->getEmail(),
                "subject" => "verification de votre compte sur le site de Pascal Monnier",
                "html_template" => "email/email_verification.html.twig",
                "context" => [
                    "user_id" => $user->getId(),
                    "token_for_email_verification" => $user->getTokenForEmailVerification(),
                    "token_for_email_verification_expires_at" => $user->getTokenForEmailVerificationExpiresAt()->format('d/m/Y ?? H:i:s')
                ]
            ]);
            return $this->redirectToRoute('visitor.registration.waiting_for_email_verification');
        }

        return $this->render('pages/visitor/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/waiting-for-email-verification', name: 'visitor.registration.waiting_for_email_verification')]
    public function waitingForEmailVerification(): Response
    {
        return $this->render("pages/visitor/registration/waiting_for_email_verification.html.twig");
    }

    #[Route('/register/email-verification/{id}/{token}', name: 'visitor.registration.email_verification')]
    public function emailverification(User $user, $token, UserRepository $userRepository)
    {
        // si l'utilisateur n'existe pas on refuse l'acces
        if (! $user) 
        {
            throw new AccessDeniedException();
        }
        
        // Si l'utilisateur a d??j?? v??rifi?? son compte, on le redirige vers la page de connection avec le petit message
        // lui disant qu'il a d??j?? v??rifi?? son compte
        if ($user->isIsVerified() ) 
        {
            $this->addFlash("warning","votre compte a d??ja ??t?? verfi??, veuillez vous connecter");
            return $this->redirectToRoute("visitor.authentication.login");
        }

        // Si le token r??cup??r?? depuis l'email est vide
        // Ou que le token qui a ??t?? ins??r?? en tant que propri??te de l'utilisateur est vide
        // Ou que le token r??cup??r?? n'est pas la m??me chose que le token de l'utilisateur
        // On refuse l'acc??s!
        if ( empty($token) || ($user->getTokenForEmailVerification() == null) || ($token !== $user->getTokenForEmailVerification() ) ) 
        {
            throw new AccessDeniedException();
         }

         // Si l'instant t durant lequel, cette v??rification du compte est en trai n d'??tre faite 
        // est > ?? la date d'expiration pr??vue, 
        // cela veut donc dire que la date d'expiration est d??j?? pass??e

         if (new \DateTimeImmutable('now') > $user->getTokenForEmailVerificationExpiresAt())
         {
            $deadline = $user->getTokenForEmailVerificationExpiresAt();
            $userRepository->remove($user,true);
            throw new CustomUserMessageAccountStatusException ("votre d??lai de validation du compte a expir?? le :$deadline. Veuillez vous inscrire ?? nouveau.");
         }

         //on verifie le compte
         $user->setIsVerified(true);

         $user->setTokenForEmailVerification('');

        //  on g??n??re la date a laquelle le compte a ??t?? verifi??
        $user->setVerifiedAt(new \DateTimeImmutable('now'));

        // On demande au userRepository de proceder a la modification
        $userRepository->save($user,true);

        $this->addFlash("success","Votre compte a ??t?? v??rifi??.Veuillez vous connecter!");

        // on redirige l 'utilisateur vers la page de connexion
        return $this->redirectToRoute("visitor.authentication.login");
    }
}

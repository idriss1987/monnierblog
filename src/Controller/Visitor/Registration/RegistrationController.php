<?php

namespace App\Controller\Visitor\Registration;



use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'visitor.registration.register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator, SendEmailService $sendEmailService): Response
    {
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
                    "token_for_email_verification_expires_at" => $user->getTokenForEmailVerificationExpiresAt()->format('d/m/Y à H:i:s')
                ]
            ]);
            return $this->redirectToRoute('visitor.registration.waiting_for_email_verification');
        }

        return $this->render('pages/visitor/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/waiting-for-email-verification', name: 'visitor.registration.waiting_for_email_verification')]
    public function emailVerification(): Response
    {
        return $this->render("pages/visitor/registration/waiting_for_email_verification.html.twig");
    }

    #[Route('/register/email-verification/{id}/{token}', name: 'visitor.registration.email_verification')]
    public function emailVerification(User $user, $token)
    {
        // Pause
    }
}

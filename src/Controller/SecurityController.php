<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Mime\Email;

use App\Form\RegistrationFormType;
use Symfony\Component\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error
            
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/forgotten_password", name="forgotten_password")
     */
    public function forgottenPassword(EntityManagerInterface $manager, Request $request, UserPasswordEncoderInterface $encoder, MailerInterface $mailer,TokenGeneratorInterface $tokenGenerator): Response 
    {
        
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            $user = $manager->getRepository(User::class)->findOneByEmail($email);

            if ($user === null) {
                $this->addFlash('danger', 'Email Inconnu');
                return $this->redirectToRoute('app_login');
            }
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $manager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_login');
            }

            $url = $this->generateUrl('reset_password', array('token'=>$token), UrlGeneratorInterface::ABSOLUTE_URL);
            
            $message = (new Email())
            ->from('kevtest9@gmail.com')
            ->to($user->getEmail())
            ->subject('Récupération de mot de passe test')
            ->text("Voice le lien de récupération de votre mot de passe: " . $url,'text/html')
            ->html("<p> Voice le lien de récupération de votre mot de passe:" .$url,'text/html' . "</p>");

            $mailer->send($message);

            $this->addFlash('info', 'Le mail de récupération de mdp a bien été envoyé, vous pouvez le consulter');
            
        }

        return $this->render('security/forgotten_password.html.twig',
        ['title'=> 'Requête mot de passe']);
    }

    /**
     * @Route("/reset_password/{token}", name="reset_password")
     */
    public function resetPassword(EntityManagerInterface $manager, string $token, Request $request,UserPasswordEncoderInterface $encoder){
        if ($request->isMethod('POST')) {
            $user = $manager->getRepository(User::class)->findOneByResetToken($token);

            if ($user === null) {
                $this->addFlash('danger', 'Email Inconnu');
                return $this->redirectToRoute('app_login');
            }
            $user->setResetToken(null);
            $user->setPassword($encoder->encodepassword($user, $request->request->get('password')));

            return $this->redirectToRoute('app_login');
            
        }else{

            return $this->render('security/reset_password.html.twig', ['token' => $token]);
        
        }
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}

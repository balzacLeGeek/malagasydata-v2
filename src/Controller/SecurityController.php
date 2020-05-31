<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use App\Form\User\RegisterType;
use App\Form\User\ResetPasswordType;
use App\Mybase\Security\User\UserUtils;
use App\Mybase\Services\Mailer;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error,
        ]);
    }
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserManager $userManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_index');
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            $userManager->registerClient($user, $plainPassword);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/reset-password", name="app_reset_password_index")
     */
    public function resetPasswordIndex(UserManager $userManager, UserRepository $userRepository, Mailer $mailer, Request $request)
    {
    	$requestEmail   = $request->get('_email');

        /**@var User $user */
        $user = $userRepository->findOneByEmail($requestEmail);

    	if ($requestEmail && $user) {
            $confirmationToken = UserUtils::generateToken();

            $siteConfig     = $this->getParameter('site');
            $mailerConfig   = $this->getParameter('mailer');

            $params = [
                "sender"    => $mailerConfig['smtp_username'],
                "pwd"       => $mailerConfig['smtp_password'],
                "sendTo"    => $user->getEmail(),
                "title"     => 'Regénération de mot de passe',
                "senderName"=> $siteConfig['name'],
            ];

            $url = $this->generateUrl('app_reset_password', [
                'token' => $confirmationToken,
                ], UrlGeneratorInterface::ABSOLUTE_URL
            );

            $html = $this->renderView("security/email-password-recovery.html.twig", [
                "fullname"  => $user->getFullname(),
                "message"   => 'Vous avez demandé à récuperer votre mot de passe. Suivez ce <strong><a href="' . $url . '">ce lien</a></strong> pour créer un nouveau mot de passe ou cliquez le boutton ci-dessous',
            ]);

            // Send email
            $mailer->sendMail($params, $html);

            // Save user
    		$user->setConfirmationToken($confirmationToken);
            $userManager->save($user);

            return $this->redirectToRoute('app_login');
    	}

        return $this->render('security/reset-password-index.html.twig');
    }
	
	/**
     * @Route("/reset-password/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, UserManager $userManager, UserRepository $userRepository, string $token)
    {
        /**@var User $user */
        $user = $userRepository->findOneByConfirmationToken($token);

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encrypt Password
        	$password = $request->request->get('reset_password')['password']['first'];
            $hash = $userManager->encryptePassword($user, $password);

            $user
                ->setPassword($hash)
                ->setConfirmationToken(NULL);

            // Save User changes
            $userManager->save($user);
            
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

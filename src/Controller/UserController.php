<?php

namespace App\Controller;

use App\Entity\PasswordReset;
use App\Form\PasswordResetPromptEmailType;
use App\Form\PasswordResetType;
use App\Repository\UserRepository;
use App\Services\SecurityHandler\SecurityHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\FormAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserController extends AbstractController
{
    /**
     * @var SecurityHandlerInterface
     */
    private $securityHandler;

    public function __construct(SecurityHandlerInterface $securityHandler)
    {
        $this->securityHandler = $securityHandler;
    }

    /**
     * @Route("/register", methods={"get", "post"}, name="app.register")
     * @inheritdoc
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, FormAuthenticator $authenticator): Response
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

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('user/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="app.login")
     * @inheritdoc
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app.homepage');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app.logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route(path="/reset-password/{token}", methods={"get", "post"}, name="app.resetPassword", defaults={"token":null})
     * @Entity(name="passwordReset", expr="repository.findOneNonUsedAndExpiredByToken(token)")
     * @inheritdoc
     */
    public function resetPassword(
        Request $request,
        UserRepository $userRepository,
        GuardAuthenticatorHandler $guardHandler,
        FormAuthenticator $authenticator,
        UserPasswordEncoderInterface $passwordEncoder,
        ?PasswordReset $passwordReset = null
    )
    {
        if ($passwordReset !== null) {
            $action = 'smile';
            $form = $this->createForm(PasswordResetType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                /** @var User $happyUser */
                $happyUser = $passwordReset->getUser();

                // change password
                $happyUser->setPassword(
                    $passwordEncoder->encodePassword(
                        $happyUser,
                        $form->get('plainPassword')->getData()
                    )
                );

                // Invalid PasswordReset
                $passwordReset->setPasswordChangedAt(new \DateTime());

                $this->getDoctrine()->getManager()->flush();

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $passwordReset->getUser(),
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            }
        }
        else {
            $action = 'cry';
            $form = $this->createForm(PasswordResetPromptEmailType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user = $userRepository->findOneBy(['email' => $form->get('email')->getData()]);
                $this->securityHandler->doAllTheNecessaryForThisUserWhoHaveLostHisPassword($user);

                $this->addFlash('resetPasswordSuccess', $user->getEmail());
                return $this->redirectToRoute('app.resetPasswordSuccess');
            }
        }

        return $this->render('user/reset-password.html.twig', [
            'form' => $form->createView(),
            'action' => $action
        ]);
    }

    /**
     * @Route(path="/reset-password-success", methods={"get"}, name="app.resetPasswordSuccess")
     * @inheritdoc
     */
    public function resetPasswordSuccess(): Response
    {
        $flashBag = $this->get('session')->getFlashBag();
        if ($flashBag->has('resetPasswordSuccess')) {
            $flashBag->get('resetPasswordSuccess');
            return $this->render('user/reset-password-success.html.twig');
        }
        return $this->redirectToRoute('app.login');
    }
}

<?php

namespace App\Service;

use App\Entity\User;
use App\EventListener\RedirectExceptionListener;
use App\Exception\RedirectException;
use App\Form\EditPasswordFormType;
use App\Form\EditProfileFormType;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class UserService
 * @package App\Service
 */
class UserService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var FormFactoryInterface
     */
    private $form;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var SendMailService
     */
    private $notify;
    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var RedirectExceptionListener
     */
    private $redirect;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $form
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param SendMailService $notify
     * @param TokenGeneratorInterface $tokenGenerator
     * @param FlashBagInterface $flashBag
     * @param RouterInterface $router
     * @param RedirectExceptionListener $redirect
     * @param SessionInterface $session
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManagerInterface $entityManager,
                                FormFactoryInterface $form,
                                UserPasswordEncoderInterface $passwordEncoder,
                                SendMailService $notify,
                                TokenGeneratorInterface $tokenGenerator,
                                FlashBagInterface $flashBag,
                                RouterInterface $router,
                                RedirectExceptionListener $redirect,
                                SessionInterface $session,
                                TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->form = $form;
        $this->passwordEncoder = $passwordEncoder;
        $this->notify = $notify;
        $this->tokenGenerator = $tokenGenerator;
        $this->flashBag = $flashBag;
        $this->router = $router;
        $this->redirect = $redirect;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Retrieves the current user id
     * @return mixed
     */
    public function getIdUser()
    {
        return $this->tokenStorage->getToken()->getUser()->getId();
    }

    /**
     * User registration
     * @param Request $request
     * @return FormInterface
     * @throws LoaderError
     * @throws RedirectException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function register(Request $request)
    {
        $countUser = $this->entityManager->getRepository(User::class)->countUser();
        $token = $this->tokenGenerator->generateToken();
        $user = new User();
        $form = $this->form->create(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setToken($token);

            if ($countUser == 0){
                $user->setRoles(['ROLE_ADMIN']);
                $user->setIsVerified(1);
            }else {
                $user->setRoles(['ROLE_USER']);
            }

            $password = $this->passwordEncoder->encodePassword(
                $user,
                $form->get('password')->getData()
            );

            $user->setPassword($password);

            $this->notify->sendMail(
                'Merci pour votre inscription',
                'contact@gmail.com',
                $form->get('email')->getData(),
                'registration/confirmation_email.html.twig',[
                    'username' => $form->get('username')->getData(),
                    'token' => $token
                ]
            );

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'Merci pour votre inscription');
            throw new RedirectException($this->router->generate('app_home'));
        }

        return $form;
    }

    /**
     * Edit profile
     * @param Request $request
     * @param $id
     * @return FormInterface
     * @throws RedirectException
     */
    public function editProfile(Request $request, $id)
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        $form = $this->form->create(EditProfileFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {

            $this->entityManager->flush();

            $this->flashBag->add('success', 'Le profil a été modifié');
            throw new RedirectException($this->router->generate('profile_home'));

        }

        return $form;
    }

    /**
     * User activation by email
     * @param $username
     * @param $token
     * @throws RedirectException
     */
    public function activation($username, $token)
    {
        $check = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

        if ($check == null){

            $this->flashBag->add('danger', 'Le token n\'est pas valable');
            throw new RedirectException($this->router->generate('app_home'));

        }else {

            if ($check->isVerified() == 1) {

                $this->flashBag->add('success', 'Compte déjà activé');

                throw new RedirectException($this->router->generate('app_home'));

            }elseif ($check->getToken() == $token && $check->getUsername() == $username) {

                $check->setIsVerified(1);
                $this->entityManager->flush();
                $this->flashBag->add('success', 'Activation du compte');

            }else {

                $this->flashBag->add('danger', 'le token n\'est pas valide');
                throw new RedirectException($this->router->generate('app_home'));

            }
        }

    }

    /**
     * Allows you to delete your account
     * @param $id
     * @throws RedirectException
     */
    public function deleteUser($id)
    {
        $idCurrent = $this->getIdUser();
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if ($idCurrent == $id) {

            $this->entityManager->remove($user);
            $this->entityManager->flush();

            $session = new Session();
            $session->invalidate();
            throw new RedirectException($this->router->generate('app_logout'));

        }

    }

    /**
     * Password Editing
     * @param Request $request
     * @param $id
     * @return FormInterface
     * @throws RedirectException
     */
    public function editPassword(Request $request, $id) {

        $user = $this->entityManager->getRepository(User::class)->find($id);

        $form = $this->form->create(EditPasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $oldPassword =  $form->get('oldPassword')->getData();

            $checkPass = $this->passwordEncoder->isPasswordValid($user, $oldPassword);

            if ($checkPass == true) {

                $user->setPassword(
                    $this->passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

                $this->entityManager->flush();

                $this->flashBag->add('success', 'Le mot de passe a été modifié');
                throw new RedirectException($this->router->generate('profile_home'));

            }else {

                $this->flashBag->add('password', 'Le mot de passe actuel n\'est pas correct');

            }

        }

        return $form;
    }

    /**
     * @param Request $request
     * @return FormInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError|RedirectException
     */
    public function resetPassword(Request $request)
    {
        $form = $this->form->create(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $mail =  $form->get('email')->getData();
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $mail]);

            if ($user == true) {
                $user = $this->entityManager->getRepository(User::class)->find($user->getId());

                $password = substr($this->tokenGenerator->generateToken(), 0, 10);

                $this->notify->sendMail(
                    'Reset password',
                    'contact@gmail.com',
                    $mail,
                    'security/reset_password_mail.html.twig',[
                        'password' => $password,
                        'email' => $mail
                    ]
                );

                $user->setPassword(
                    $this->passwordEncoder->encodePassword(
                        $user,
                        $password
                    )
                );

                $this->entityManager->flush();
                $this->flashBag->add('success', 'Réinitialisation du mot de passe, vérifiez vos e-mails');
                throw new RedirectException($this->router->generate('app_login'));

            }else {
                $this->flashBag->add('danger', 'Email inconnu');
            }

        }

        return $form;
    }
}

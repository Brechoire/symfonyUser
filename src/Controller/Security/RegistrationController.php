<?php

namespace App\Controller\Security;

use App\Exception\RedirectException;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class RegistrationController
 * @package App\Controller\Security
 */
class RegistrationController extends AbstractController
{
     /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserService $userService
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError|RedirectException
      */
    public function register(Request $request, UserService $userService): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $form = $userService->register($request);

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

}

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
 * Class ResetPasswordController
 * @package App\Controller\Security
 */
class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/reset-password", name="app_reset_password")
     * @param Request $request
     * @param UserService $userService
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError|RedirectException
     */
    public function index(Request $request, UserService $userService): Response
    {
        $form = $userService->resetPassword($request);

        return $this->render('/security/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
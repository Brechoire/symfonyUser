<?php

namespace App\Controller\Security;

use App\Exception\RedirectException;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ActivationController
 * @package App\Controller\Security
 */
class ActivationController extends AbstractController
{
    /**
     * @Route("/activation/{username}/{token}", name="app_activation")
     * @param $username
     * @param $token
     * @param UserService $userService
     * @return Response
     * @throws RedirectException
     */
    public function index($username, $token, UserService $userService): Response
    {
        $userService->activation($username, $token);

        return $this->redirectToRoute('app_home');
    }
}
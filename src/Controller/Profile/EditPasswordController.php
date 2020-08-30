<?php

namespace App\Controller\Profile;

use App\Exception\RedirectException;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EditPasswordController
 * @package App\Controller\Profile
 */
class EditPasswordController extends AbstractController
{
    /**
     * @Route("/profile/edit-password/{id}/{token}", name="profile_edit_password", requirements={"id"="\d+"})
     * @param Request $request
     * @param UserService $userService
     * @return Response
     * @throws RedirectException
     */
    public function index(Request $request, UserService $userService): Response
    {
        $submittedToken = $request->get('token');

        if ($this->isCsrfTokenValid('profile_edit_password', $submittedToken)) {

            $form = $userService->editPassword($request, $this->getUser()->getId());

        }

        return $this->render('profile/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
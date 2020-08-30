<?php

namespace App\Controller\Profile;

use App\Exception\RedirectException;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EditProfileController
 * @package App\Controller\Profile
 */
class EditProfileController extends AbstractController
{
    /**
     * @Route("/profile/edit-profile/{id}/{token}", name="profile_edit_profile", requirements={"id"="\d+"})
     * @param Request $request
     * @param UserService $userService
     * @return Response
     * @throws RedirectException
     */
    public function index(Request $request, UserService $userService): Response
    {
        $submittedToken = $request->get('token');

        if ($this->isCsrfTokenValid('profile_edit_profile', $submittedToken)) {

            $form = $userService->editProfile($request, $this->getUser()->getId());

        }

        return $this->render('profile/edit_profile.html.twig', [
            'form' => $form->createView()
        ]);

    }
}
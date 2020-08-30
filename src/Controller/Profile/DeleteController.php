<?php

namespace App\Controller\Profile;

use App\Exception\RedirectException;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DeleteController
 * @package App\Controller\Profile
 */
class DeleteController extends AbstractController
{
    /**
     * @Route("/profile/delete/{id}/{token}", name="profile_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @param UserService $userService
     * @return Response
     * @throws RedirectException
     */
    public function index(Request $request, $id, UserService $userService): Response
    {
        $submittedToken = $request->get('token');

        if ($this->isCsrfTokenValid('profile_delete', $submittedToken)) {

            $userService->deleteUser($id);

        }

        return $this->redirectToRoute('app_home');
    }
}
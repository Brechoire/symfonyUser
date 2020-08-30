<?php

namespace App\Controller\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller\Profile
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/profile", name="profile_home")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('profile/home.html.twig');
    }
}
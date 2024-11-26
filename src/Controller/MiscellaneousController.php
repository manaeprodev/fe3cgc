<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MiscellaneousController extends AbstractController
{
    #[Route('/misc/leaderboard', name: 'app_leaderboard')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function leaderboard(Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('miscellaneous/leaderboard.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/misc/help', name: 'app_help')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function help(Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('miscellaneous/help.html.twig', [
            'user' => $user,
        ]);
    }
}
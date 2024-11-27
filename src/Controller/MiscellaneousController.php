<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MiscellaneousController extends AbstractController
{
    #[Route('/misc/leaderboard', name: 'app_leaderboard')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function leaderboard(Security $security, UserRepository $userRepository): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $leaderboard = $userRepository->getLeaderboard($offset, $limit);

        $totalResults = $userRepository->getTotalUsers();
        $totalPages = ceil($totalResults / $limit);
        return $this->render('miscellaneous/leaderboard.html.twig', [
            'user' => $user,
            'usersAsLeaderboard' => $leaderboard,
            'currentPage' => $page,
            'totalResults' => $totalResults,
            'totalPages' => $totalPages
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
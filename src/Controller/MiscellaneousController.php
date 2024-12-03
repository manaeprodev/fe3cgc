<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TitleRepository;
use App\Repository\UnitRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

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

    #[Route('/misc/profile', name: 'app_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(Security $security, TitleRepository $titleRepository, EntityManagerInterface $emi, UserRepository $userRepository) : Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userTitle = $userRepository->getUserTitleFamily($user->getId());

        $userFamily = $userTitle['family'];

        $promotableClasses = $titleRepository->getPromotableClassesByUserTitleId($userFamily);

        $promoClasses = [];
        $changeClasses = [];
        
        foreach ($promotableClasses as $item) {
            if ($item['family'] == $userFamily) {
                $promoClasses[] = $item;
            } else {
                $changeClasses[] = $item;
            }
        }
        
        return $this->render('miscellaneous/profile.html.twig', [
            'user' => $user,
            'promotableClasses' => $promoClasses,
            'changeableClasses' => $changeClasses
        ]);
    }

    #[Route('/misc/update/avatar', name: 'app_update_avatar', methods: "POST")]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function updateAvatar(Request $request, Security $security, CsrfTokenManagerInterface $csrfTokenManagerInterface, EntityManagerInterface $emi) : JsonResponse
    {
        $csrfToken = $request->headers->get('X-CSRF-Token');
        if (!$csrfTokenManagerInterface->isTokenValid(new CsrfToken('profile', $csrfToken))) {
            return new JsonResponse(['error' => 'Token CSRF invalide'], 400);
        }

        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $data = $request->toArray();
        $user->setAvatar($data['imageName']);

        $emi->flush();

        return new JsonResponse(['message' => 'Avatar successfully updated']);
    }
}
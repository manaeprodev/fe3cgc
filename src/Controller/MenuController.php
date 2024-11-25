<?php

namespace App\Controller;

use App\Repository\TerritoryRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(TerritoryRepository $territoryRepository): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();

        if($user->isFirstCo()) {
            return new RedirectResponse('/register/finish/' . $userId . '/1');
        }

        $territories = $territoryRepository->findAll();
        $count = $territoryRepository->countTerritories();

        return $this->render('menu/index.html.twig', [
            'territories' => $territories,
            'count' => $count
        ]);
    }

    #[Route('/menu/status', name: 'menu_status', methods: "POST")]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function status(Request $request, CsrfTokenManagerInterface $csrfTokenManagerInterface, TerritoryRepository $territoryRepository, UserRepository $userRepository): JsonResponse
    {
        $csrfToken = $request->headers->get('X-CSRF-Token');
        if (!$csrfTokenManagerInterface->isTokenValid(new CsrfToken('menu_status', $csrfToken))) {
            return new JsonResponse(['error' => 'Token CSRF invalide'], 400);
        }

        $data = $request->toArray();
        $defCoef = 0.8;
        $terCoef = 0.6;

        $name = $data['name'] ?? null;

        if ($name === null) {
            return new JsonResponse(['error' => 'Cannot find territory.'], 404);
        }

        $territory = $territoryRepository->getTerritoryByName($name);
        $territoryRenown = $territory->getRenownPrize();
        $territoryFaction = $territory->getFaction();
        $territoryFactionId = $territoryFaction->getId();
        $territoryId = $territory->getId();

        $usersOnTerritory = $userRepository->getUnitsByTerritoryId($territoryId);
        $countUnitsOnTerritory = count($usersOnTerritory)-1;
        if($countUnitsOnTerritory >= 10) {
            $countUnitsOnTerritory = 10;
        }

        $defendersRenown = 0;
        $attackersRenown = 0;

        $totalBonus = 1;

        foreach($usersOnTerritory as $user) {
            if($user['factionId'] == $territoryFactionId) {
                // Defense
                $defendersRenown += $user['renown'];
                
            } else {
                // Offense
                $attackersRenown += $user['renown'];
            }

            switch($user['titleId']) {
                case 1:
                    if($user['factionId'] != $territoryFactionId) {
                        $totalBonus = $totalBonus * 1.10;
                    }
                    break;
                case 2:
                    if($user['factionId'] != $territoryFactionId) {
                        $totalBonus = $totalBonus * 1.15;
                    }
                    break;
                case 3:
                    if($user['factionId'] != $territoryFactionId) {
                        $totalBonus = $totalBonus * 1.20;
                    }
                    break;
                case 7:
                    if($user['factionId'] == $territoryFactionId) {
                        $totalBonus = $totalBonus * 0.90;
                    }
                    break;
                case 8:
                    if($user['factionId'] == $territoryFactionId) {
                        $totalBonus = $totalBonus * 0.85;
                    }
                    break;
                case 9:
                    if($user['factionId'] == $territoryFactionId) {
                        $totalBonus = $totalBonus * 0.80;
                    }
                    break;
                case 10:
                    if($user['factionId'] == $territoryFactionId) {
                        $totalBonus = $totalBonus * (1 - $countUnitsOnTerritory * 1 / 100);
                    }
                    break;
                case 11:
                    if($user['factionId'] == $territoryFactionId) {
                        $totalBonus = $totalBonus * (1 - $countUnitsOnTerritory * 2 / 100);
                    }
                    break;
                case 12:
                    if($user['factionId'] == $territoryFactionId) {
                        $totalBonus = $totalBonus * (1 - $countUnitsOnTerritory * 3 / 100);
                    }   
                    break;
                case 13:
                    if($user['factionId'] != $territoryFactionId) {
                        $totalBonus = $totalBonus * (1 + $countUnitsOnTerritory * 1 / 100);
                    }      
                    break;
                case 14:
                    if($user['factionId'] != $territoryFactionId) {
                        $totalBonus = $totalBonus * (1 + $countUnitsOnTerritory * 2 / 100);
                    }
                    break;
                case 15:
                    if($user['factionId'] != $territoryFactionId) {
                        $totalBonus = $totalBonus * (1 + $countUnitsOnTerritory * 3 / 100);
                    }
                    break;
                case 19:
                case 20:
                case 21:
                case 23:
                    $totalBonus = $totalBonus * 0.65;
                    break;
                case 22:
                    $totalBonus = $totalBonus * 0.60;
                    break;
                default:
                    break;
            }
        }
        
        $neighborData = [];
        $neighborsRenown = 0;

        foreach ($territory->getNeighboringTerritories() as $neighbor) {
            // Accéder à la factionId et au name du voisin
            $neighborData[] = [
                'name' => $neighbor->getName(),
                'factionId' => $neighbor->getFaction() ? $neighbor->getFaction()->getId() : null,
                'factionName' => $neighbor->getFaction() ? $neighbor->getFaction()->getName() : null,
                'renown' => $neighbor->getRenownPrize() ? $neighbor->getRenownPrize() : 0
            ];

            if($territoryFactionId == $neighbor->getFaction()->getId()) {
                $defendersRenown += $neighbor->getRenownPrize();
            }
            
        }

        $currentSuccessRate = min(round(100*(($attackersRenown*$totalBonus)/(($defendersRenown*$defCoef)+($territoryRenown*$terCoef))), 2), 100);

        $defenders = array_values(array_filter($usersOnTerritory, fn($user) => $user['factionId'] == $territoryFactionId));
        $attackers = array_values(array_filter($usersOnTerritory, fn($user) => $user['factionId'] != $territoryFactionId));

        return new JsonResponse(['message' => 'Territory status retrieved.', 
            'id' => $territoryId, 
            'name' => $name, 
            'renown' => $territoryRenown, 
            'money' => $territory->getMoneyPrize(), 
            'successRate' => $currentSuccessRate, 
            'attackersRenown' => $attackersRenown,
            'totalBonus' => $totalBonus,
            'defendersRenown' => $defendersRenown,
            'defCoef' => $defCoef,
            'terCoef' => $terCoef,
            'territoryRenown' => $territoryRenown,
            'factionId' => $territoryFactionId, 
            'neighborsData' => $neighborData, 
            'attackers' => $attackers, 
            'defenders' => $defenders], 200);
    }
}

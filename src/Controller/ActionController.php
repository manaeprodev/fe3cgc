<?php

namespace App\Controller;

use App\Entity\Battle;
use App\Entity\Faction;
use App\Entity\Territory;
use App\Repository\BattleRepository;
use App\Repository\UnitRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ActionController extends AbstractController
{
    #[Route('/action', name: 'user_action', methods: "POST")]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function action(Request $request, CsrfTokenManagerInterface $csrfTokenManagerInterface, UserRepository $userRepository, BattleRepository $battleRepository, EntityManagerInterface $emi): JsonResponse
    {
        $csrfToken = $request->headers->get('X-CSRF-Token');
        if (!$csrfTokenManagerInterface->isTokenValid(new CsrfToken('menu_status', $csrfToken))) {
            return new JsonResponse(['error' => 'Token CSRF invalide'], 400);
        }

        $data = $request->toArray();
        $result = "";

        $userId = $data['userId'] ?? null;
        $userRenown = $data['userRenown'] ?? null;
        $userFactionId = $data['userFactionId'] ?? null;
        $territoryId = $data['territoryId'] ?? null;
        $action = $request->headers->get('Action');

        if($action === "attack") {
            $result = "Attack";
            //checkAuthorization();
            $existingBattle = $battleRepository->getExistingBattle($territoryId);

            if($existingBattle) {
                //UPdate
            } else {
                $territory = $emi->getRepository(Territory::class)->find($territoryId);
                $faction = $emi->getRepository(Faction::class)->find($userFactionId);

                $battle = new Battle();
                $battle->setTerritoryId($territory);
                $battle->setStatus(1);
                $battle->setStartDateTime(new \DateTime());
                $battle->setEndDateTime((new \DateTime())->modify('+2 days'));
                $battle->setAttackerFactionId($faction);

                $emi->persist($battle);
                $emi->flush();
            }

        } elseif($action === "defend") {
            $result = "Defend";
            //checkAuthorization();
        } else {
            return new JsonResponse(['error' => 'Unknown action : only "attack" and "defend" are supported.'], 404);
        }

        $userRepository->positionUnit($userId, $territoryId);

        $result = $result . " successfully registered.";

        return new JsonResponse(['message' => $result]);
    }
}

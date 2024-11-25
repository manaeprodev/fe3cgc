<?php

namespace App\Controller;

use App\Repository\BattleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class BattleController extends AbstractController
{
    #[Route('/battle/end', name: 'app_battle')]
    public function index(BattleRepository $battleRepository, UserRepository $userRepository, EntityManagerInterface $emi): JsonResponse
    {
        $endedBattles = $battleRepository->updateBattlesStatusToFinished();

        foreach($endedBattles as $battle) {

            $defendersRenown = 0;
            $attackersRenown = 0;
            $totalBonus = 1;
            $defCoef = 0.8;
            $terCoef = 0.6;

            // Recup attackers & defenders & territory & territoryNeighbors
            $territory = $battle->getTerritoryId();
            $territoryId = $territory->getId();
            $territoryRenown = $territory->getRenownPrize();
            $territoryMoney = $territory->getMoneyPrize();
            $defenderFactionId = $territory->getFaction()->getId();
            $attackersFaction = $battle->getAttackerFactionId();
            $attackerFactionId = $attackersFaction->getId();

            $unitsOnTerritory = $userRepository->getUnitsByTerritoryId($territoryId);
            $countUnitsOnTerritory = count($unitsOnTerritory)-1;
            if($countUnitsOnTerritory >= 10) {
                $countUnitsOnTerritory = 10;
            }
            foreach($unitsOnTerritory as $unit) {
                $unitFactionId = $unit['factionId'];

                if($unitFactionId == $defenderFactionId) {
                    $defendersRenown += $unit['renown'];
                } else {
                    $attackersRenown += $unit['renown'];
                }

                switch($unit['titleId']) {
                    case 1:
                        if($unit['factionId'] != $defenderFactionId) {
                            $totalBonus = $totalBonus * 1.10;
                        }
                        break;
                    case 2:
                        if($unit['factionId'] != $defenderFactionId) {
                            $totalBonus = $totalBonus * 1.15;
                        }
                        break;
                    case 3:
                        if($unit['factionId'] != $defenderFactionId) {
                            $totalBonus = $totalBonus * 1.20;
                        }
                        break;
                    case 7:
                        if($unit['factionId'] == $defenderFactionId) {
                            $totalBonus = $totalBonus * 0.90;
                        }
                        break;
                    case 8:
                        if($unit['factionId'] == $defenderFactionId) {
                            $totalBonus = $totalBonus * 0.85;
                        }
                        break;
                    case 9:
                        if($unit['factionId'] == $defenderFactionId) {
                            $totalBonus = $totalBonus * 0.80;
                        }
                        break;
                    case 10:
                        if($unit['factionId'] == $defenderFactionId) {
                            $totalBonus = $totalBonus * (1 - $countUnitsOnTerritory * 1 / 100);
                        }
                        break;
                    case 11:
                        if($unit['factionId'] == $defenderFactionId) {
                            $totalBonus = $totalBonus * (1 - $countUnitsOnTerritory * 2 / 100);
                        }
                        break;
                    case 12:
                        if($unit['factionId'] == $defenderFactionId) {
                            $totalBonus = $totalBonus * (1 - $countUnitsOnTerritory * 3 / 100);
                        }   
                        break;
                    case 13:
                        if($unit['factionId'] != $defenderFactionId) {
                            $totalBonus = $totalBonus * (1 + $countUnitsOnTerritory * 1 / 100);
                        }      
                        break;
                    case 14:
                        if($unit['factionId'] != $defenderFactionId) {
                            $totalBonus = $totalBonus * (1 + $countUnitsOnTerritory * 2 / 100);
                        }
                        break;
                    case 15:
                        if($unit['factionId'] != $defenderFactionId) {
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
            
            foreach($territory->getNeighboringTerritories() as $neighbor) {
                $neighborData[] = [
                    'name' => $neighbor->getName(),
                    'factionId' => $neighbor->getFaction() ? $neighbor->getFaction()->getId() : null,
                    'factionName' => $neighbor->getFaction() ? $neighbor->getFaction()->getName() : null,
                    'renown' => $neighbor->getRenownPrize() ? $neighbor->getRenownPrize() : 0
                ];
    
                if($defenderFactionId == $neighbor->getFaction()->getId()) {
                    $defendersRenown += $neighbor->getRenownPrize();
                }
            }
            // Calcul du gagnant
            $totalRenown = $attackersRenown + $defendersRenown;

            $currentSuccessRate = min(round(100*(($attackersRenown*$totalBonus)/(($defendersRenown*$defCoef)+($territoryRenown*$terCoef))), 2), 100);
            // Attribue les récompenses

            $battle->setAttackersSuccessRate($currentSuccessRate);

            $scaledPercentage = $currentSuccessRate * 100;

            $randomNumber = mt_rand(0, 10000);


            $defenders = array_values(array_filter($unitsOnTerritory, fn($user) => $user['factionId'] == $defenderFactionId));
            $attackers = array_values(array_filter($unitsOnTerritory, fn($user) => $user['factionId'] != $defenderFactionId));

            $nbOfAttackers = count($attackers);

            if($randomNumber <= $scaledPercentage) {
                //Attackers win
                $outcome = 1;
                //Territory must become the attacker's property
                $territory->setFaction($attackersFaction);
                //Defenders lose 200 money & Defenders lose their position
                foreach($defenders as $defender) {
                    $user = $userRepository->findOneBy(['id' => $defender['id']]);
                    $user->setMoney($user->getMoney() - 200);
                    $user->setTerritory(null);

                    $emi->flush($user);
                }

                //Attackers win (Money Prize + bonuses) + (Renown Prize + bonuses)
                foreach($attackers as $attacker) {
                    $user = $userRepository->findOneBy(['id' => $attacker['id']]);

                    $moneyCoef = 1;
                    $renownCoef = 1;

                    switch($unit['titleId']) {
                        case 4:
                            $renownCoef = 1.1;
                            break;
                        case 5:
                            $renownCoef = 1.2;
                            break;
                        case 6:
                            $renownCoef = 1.3;
                            break;
                        case 16:
                            $moneyCoef = 1.15;
                            break;
                        case 17:
                            $moneyCoef = 1.2;
                            break;
                        case 18:
                            $moneyCoef = 1.25;
                            break;
                        default:
                            break;
                    }

                    $user->setRenown(round($user->getRenown() + (($territoryRenown / $nbOfAttackers) * $renownCoef)));
                    $user->setMoney(round($user->getMoney() + (($territoryMoney / $nbOfAttackers) * $moneyCoef)));

                    $emi->flush($user);
                }
                
                $territory->setRenownPrize($territoryRenown*1.005);
                
            } else {
                //Defenders win
                $outcome = 2;
                //Defenders win 300 money & renown = 5% territory renown + bonuses
                foreach($defenders as $defender) {

                    $user = $userRepository->findOneBy(['id' => $defender['id']]);
                    
                    switch($unit['titleId']) {
                        case 4:
                            $renownCoef = 1.1;
                            break;
                        case 5:
                            $renownCoef = 1.2;
                            break;
                        case 6:
                            $renownCoef = 1.3;
                            break;
                        case 16:
                            $moneyCoef = 1.15;
                            break;
                        case 17:
                            $moneyCoef = 1.2;
                            break;
                        case 18:
                            $moneyCoef = 1.25;
                            break;
                        default:
                            break;
                    }

                    $user->setRenown(round($user->getRenown() + (($territoryRenown / 0.05) * $renownCoef)));
                    $user->setMoney(round($user->getMoney() + (300 * $moneyCoef)));
                }
                //Attackers lose 300 money & Attackers lose their position
                foreach($attackers as $attacker) {
                    $user = $userRepository->findOneBy(['id' => $attacker['id']]);
                    $user->setMoney($user->getMoney() - 300);
                    $user->setTerritory(null);

                    $emi->flush($user);
                }
                
                $territory->setRenownPrize($territory->getRenownPrize()*1.01);
            }

            $battle->setOutcome($outcome);

            $emi->flush($battle);
            $emi->flush($territory);

        }


        return new JsonResponse(['message' => 'Battles successfully ended.']);
    }

    #[Route('/battle/reset', name: 'app_reset')]
    public function reset(): JsonResponse
    {


        return new JsonResponse(['message' => 'Game successfully reset.']);
    }
}

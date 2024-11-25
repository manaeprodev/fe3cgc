<?php

namespace App\Entity;

use App\Repository\BattleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BattleRepository::class)]
class Battle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column(nullable: true)]
    private ?float $attackersSuccessRate = null;

    #[ORM\Column(nullable: true)]
    private ?int $outcome = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDateTime = null;

    #[ORM\ManyToOne(inversedBy: 'battles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Territory $territoryId = null;

    #[ORM\ManyToOne(inversedBy: 'battles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Faction $attackerFactionId = null;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAttackersSuccessRate(): ?float
    {
        return $this->attackersSuccessRate;
    }

    public function setAttackersSuccessRate(float $attackersSuccessRate): static
    {
        $this->attackersSuccessRate = $attackersSuccessRate;

        return $this;
    }

    public function getOutcome(): ?int
    {
        return $this->outcome;
    }

    public function setOutcome(?int $outcome): static
    {
        $this->outcome = $outcome;

        return $this;
    }

    public function getStartDateTime(): ?\DateTimeInterface
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(\DateTimeInterface $startDateTime): static
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getEndDateTime(): ?\DateTimeInterface
    {
        return $this->endDateTime;
    }

    public function setEndDateTime(\DateTimeInterface $endDateTime): static
    {
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function getTerritoryId(): ?Territory
    {
        return $this->territoryId;
    }

    public function setTerritoryId(?Territory $territoryId): static
    {
        $this->territoryId = $territoryId;

        return $this;
    }

    public function getAttackerFactionId(): ?Faction
    {
        return $this->attackerFactionId;
    }

    public function setAttackerFactionId(?Faction $attackerFactionId): static
    {
        $this->attackerFactionId = $attackerFactionId;

        return $this;
    }
}

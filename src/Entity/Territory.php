<?php

namespace App\Entity;

use App\Repository\TerritoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TerritoryRepository::class)]
class Territory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $money_prize = null;

    #[ORM\Column(nullable: true)]
    private ?int $renown_prize = null;

    #[ORM\ManyToMany(targetEntity: 'App\Entity\Territory', inversedBy: 'neighbors')]
    #[ORM\JoinTable(name: 'territories_neighbors')]
    #[ORM\JoinColumn(name: 'territory_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'neighboring_territory_id', referencedColumnName: 'id')]
    private Collection $neighboringTerritories;

    #[ORM\ManyToOne(targetEntity: Faction::class)]
    #[ORM\JoinColumn(name: "faction_id", referencedColumnName: "id", nullable: true)]
    private ?Faction $faction = null;

    /**
     * @var Collection<int, Battle>
     */
    #[ORM\OneToMany(targetEntity: Battle::class, mappedBy: 'territoryId')]
    private Collection $battles;

    public function __construct()
{
    $this->neighboringTerritories = new ArrayCollection();
    $this->battles = new ArrayCollection();
}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getMoneyPrize(): ?int
    {
        return $this->money_prize;
    }

    public function setMoneyPrize(?int $money_prize): static
    {
        $this->money_prize = $money_prize;

        return $this;
    }

    public function getRenownPrize(): ?int
    {
        return $this->renown_prize;
    }

    public function setRenownPrize(?int $renown_prize): static
    {
        $this->renown_prize = $renown_prize;

        return $this;
    }

    public function getNeighboringTerritories(): Collection
    {
        return $this->neighboringTerritories;
    }

    public function hasNeighbor(Territory $territory): bool
    {
        return $this->neighboringTerritories->contains($territory);
    }

    public function getFaction(): ?Faction
    {
        return $this->faction;
    }

    public function setFaction(?Faction $faction): static
    {
        $this->faction = $faction;

        return $this;
    }

    /**
     * @return Collection<int, Battle>
     */
    public function getBattles(): Collection
    {
        return $this->battles;
    }

    public function addBattle(Battle $battle): static
    {
        if (!$this->battles->contains($battle)) {
            $this->battles->add($battle);
            $battle->setTerritoryId($this);
        }

        return $this;
    }

    public function removeBattle(Battle $battle): static
    {
        if ($this->battles->removeElement($battle)) {
            // set the owning side to null (unless already changed)
            if ($battle->getTerritoryId() === $this) {
                $battle->setTerritoryId(null);
            }
        }

        return $this;
    }
}

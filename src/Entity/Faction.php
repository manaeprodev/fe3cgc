<?php

namespace App\Entity;

use App\Repository\FactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactionRepository::class)]
class Faction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $icon = null;

    #[ORM\Column(length: 255)]
    private ?string $leader_avatar = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $leaderSpeech = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $leaderCritical = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $leaderHopeful = null;

    /**
     * @var Collection<int, Battle>
     */
    #[ORM\OneToMany(targetEntity: Battle::class, mappedBy: 'attackerFactionId')]
    private Collection $battles;

    public function __construct()
    {
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

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getLeaderAvatar(): ?string
    {
        return $this->leader_avatar;
    }

    public function setLeaderAvatar(string $leader_avatar): static
    {
        $this->leader_avatar = $leader_avatar;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLeaderSpeech(): ?string
    {
        return $this->leaderSpeech;
    }

    public function setLeaderSpeech(?string $leaderSpeech): static
    {
        $this->leaderSpeech = $leaderSpeech;

        return $this;
    }

    public function getLeaderCritical(): ?string
    {
        return $this->leaderCritical;
    }

    public function setLeaderCritical(?string $leaderCritical): static
    {
        $this->leaderCritical = $leaderCritical;

        return $this;
    }

    public function getLeaderHopeful(): ?string
    {
        return $this->leaderHopeful;
    }

    public function setLeaderHopeful(?string $leaderHopeful): static
    {
        $this->leaderHopeful = $leaderHopeful;

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
            $battle->setAttackerFactionId($this);
        }

        return $this;
    }

    public function removeBattle(Battle $battle): static
    {
        if ($this->battles->removeElement($battle)) {
            // set the owning side to null (unless already changed)
            if ($battle->getAttackerFactionId() === $this) {
                $battle->setAttackerFactionId(null);
            }
        }

        return $this;
    }
}

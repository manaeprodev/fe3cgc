<?php

namespace App\Entity;

use App\Repository\TitleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TitleRepository::class)]
class Title
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $bonusLabel = null;

    #[ORM\Column]
    private ?int $level = null;

    #[ORM\Column(length: 255)]
    private ?string $miniLabel = null;

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

    public function getBonusLabel(): ?string
    {
        return $this->bonusLabel;
    }

    public function setBonusLabel(string $bonusLabel): static
    {
        $this->bonusLabel = $bonusLabel;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getMiniLabel(): ?string
    {
        return $this->miniLabel;
    }

    public function setMiniLabel(string $miniLabel): static
    {
        $this->miniLabel = $miniLabel;

        return $this;
    }
}

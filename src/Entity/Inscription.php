<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sortie $sortie_id = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_inscription = null;

    /**
     * @param int|null $id
     * @param Sortie|null $sortie_id
     * @param User|null $user_id
     * @param \DateTimeInterface|null $date_inscription
     */
    public function __construct(?int $id, ?Sortie $sortie_id, ?User $user_id, ?\DateTimeInterface $date_inscription)
    {
        $this->id = $id;
        $this->sortie_id = $sortie_id;
        $this->user_id = $user_id;
        $this->date_inscription = $date_inscription;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSortieId(): ?Sortie
    {
        return $this->sortie_id;
    }

    public function setSortieId(?Sortie $sortie_id): self
    {
        $this->sortie_id = $sortie_id;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDateInscription(\DateTimeInterface $date_inscription): self
    {
        $this->date_inscription = $date_inscription;

        return $this;
    }

    public function __toString():string
    {
        return $this->id;
    }
}

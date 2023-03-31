<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class rechercheSortie
{

    private ?Campus $campus = null;

    private ?string $q = null;

    private ?\DateTimeInterface $datemin = null;

    private ?\DateTimeInterface $datemax = null;

    private ?bool $organisateur =false;

    private ?bool $inscrit = false;

    private ?bool $noninscrit = false;

    private ?bool $sortiepassee = false;

    /**
     * @return Campus|null
     */
    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus|null $campus
     */
    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return string|null
     */
    public function getQ(): ?string
    {
        return $this->q;
    }

    /**
     * @param string|null $q
     */
    public function setQ(?string $q): void
    {
        $this->q = $q;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDatemin(): ?\DateTimeInterface
    {
        return $this->datemin;
    }

    /**
     * @param \DateTimeInterface|null $datemin
     */
    public function setDatemin(?\DateTimeInterface $datemin): void
    {
        $this->datemin = $datemin;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDatemax(): ?\DateTimeInterface
    {
        return $this->datemax;
    }

    /**
     * @param \DateTimeInterface|null $datemax
     */
    public function setDatemax(?\DateTimeInterface $datemax): void
    {
        $this->datemax = $datemax;
    }

    /**
     * @return bool|null
     */
    public function getOrganisateur(): ?bool
    {
        return $this->organisateur;
    }

    /**
     * @param bool|null $organisateur
     */
    public function setOrganisateur(?bool $organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    /**
     * @return bool|null
     */
    public function getInscrit(): ?bool
    {
        return $this->inscrit;
    }

    /**
     * @param bool|null $inscrit
     */
    public function setInscrit(?bool $inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    /**
     * @return bool|null
     */
    public function getNoninscrit(): ?bool
    {
        return $this->noninscrit;
    }

    /**
     * @param bool|null $noninscrit
     */
    public function setNoninscrit(?bool $noninscrit): void
    {
        $this->noninscrit = $noninscrit;
    }

    /**
     * @return bool|null
     */
    public function getSortiepassee(): ?bool
    {
        return $this->sortiepassee;
    }

    /**
     * @param bool|null $sortiepassee
     */
    public function setSortiepassee(?bool $sortiepassee): void
    {
        $this->sortiepassee = $sortiepassee;
    }

//public function __toString(): string
//{
//    return $this -> q;
//}


}
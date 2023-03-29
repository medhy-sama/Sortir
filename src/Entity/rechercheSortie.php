<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class rechercheSortie
{

    private ?Campus $campus = null;

    private ?string $q ='';

    private ?\DateTimeInterface $datemin = null;

    private ?\DateTimeInterface $datemax = null;

    private ?bool $organisateur =true;

    private ?bool $inscrit = true;

    private ?bool $noninscrit = true;

    private ?bool $sortiepassee = false;


}
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/Sortie', name: 'sortie')] // prefixe
class SortieController extends AbstractController
{
    #[Route('/creer', name: '_creer')]
    public function creerSortie(): Response
    {
        return $this->render('sortie/creer.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    #[Route('/detail', name: '_detail')]
    public function afficher(): Response
    {
        return $this->render('sortie/detail.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    #[Route('/modifier', name: '_modifier')]
    public function modifierSortie(): Response
    {
        return $this->render('sortie/modifier.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    #[Route('/annuler', name: '_annuler')]
    public function annulerSortie(): Response
    {
        return $this->render('sortie/annuler.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }
}

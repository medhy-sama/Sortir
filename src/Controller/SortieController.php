<?php

namespace App\Controller;

use App\Entity\rechercheSortie;
use App\Entity\Sortie;
use App\Form\RechercheSortieType;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: '_list', methods: ['GET'])]
    public function listeSortie(SortieRepository $sortieRepository,
                                Request $request): Response
    {
        $rechercheSortie = new rechercheSortie();

        //$rechercheSortie -> setCampus = $request->get('campus');
        $form = $this->createForm(RechercheSortieType::class, $rechercheSortie);
       // $form->handleRequest($request);

       $sorties =  $sortieRepository->findAll();
        return $this->render('sortie/liste.html.twig',
                compact('sorties','form')
        );
    }

    #[Route('/creer', name: '_creer', methods: ['GET', 'POST'])]
    public function creerSortie(Request $request,
                                EntityManagerInterface $em,
                                ): Response

    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $sortie->setOrganisateur($this->getUser());
                $em->persist($sortie);
                $em->flush();
                $this->addFlash('succes', 'Votre souhait a été enregistré');
                return $this->redirectToRoute('_list', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $exception) {
                return $this->redirectToRoute('_creer');
            }
        }
        return $this->renderForm('sortie/creer.html.twig', compact('sortie','form')
        );
    }

    #[Route('/{sortie}', name: '_detail', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {


        return $this->render('sortie/detail.html.twig',
                            compact('sortie'));
    }

    #[Route('/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->save($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }


}

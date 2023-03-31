<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\rechercheSortie;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\RechercheSortieType;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
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
                                EtatRepository $etatRepository,
                                CampusRepository $campusRepository,
//                                UserInterface $user,
                                ): Response

    {
        $sortie = new Sortie();

        $sortie->setCampus($campusRepository->find($this->getUser()->getCampus()->getId()));
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setOrganisateur($this->getUser());
            $sortie->setEtat($etatRepository->find(1));
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('succes', 'Votre sortie a été enregistré');
            return $this->redirectToRoute('_list', [], Response::HTTP_SEE_OTHER);
//            try {
//
//            } catch (\Exception $exception) {
//                return $this->redirectToRoute('_creer');
//            }
        }
        return $this->render('sortie/creer.html.twig', compact('sortie','form')
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

            return $this->redirectToRoute('app_sortie_edit', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/edit.html.twig', [
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

        return $this->redirectToRoute('app_sortie_delete', [], Response::HTTP_SEE_OTHER);
    }


}

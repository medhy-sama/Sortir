<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\rechercheSortie;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SearchType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted("ROLE_USER")]
#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: '_list', methods: ['GET','POST'])]
    public function listeSortie(SortieRepository $sortieRepository,
                                EtatRepository $etatRepository,
                                Request $request,
                                ): Response
    {
//        $sorties = $sortieRepository->findAll();
        $etatpasse = $etatRepository->find(5);
        $rechercheSortie = new rechercheSortie();
        $user = $this->getUser();
        $form = $this->createForm(SearchType::class,$rechercheSortie);
        $form->handleRequest($request);

            return $this->render('sortie/liste.html.twig', [
                'sorties' => $sortieRepository->search($rechercheSortie, $user,$etatpasse),
                'form' => $form
            ]);
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
        return $this->renderForm('sortie/creer.html.twig', compact('sortie', 'form')
        );
    }

    #[Route('/{sortie}', name: '_detail', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {


        return $this->render('sortie/detail.html.twig',
            compact('sortie'));
    }

    #[Route('/edit/{id}', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->save($sortie, true);

            return $this->redirectToRoute('_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/inscrire/{sortie}', name: '_inscrire', methods: ['GET'])]
    public function inscription(Sortie $sortie, SortieRepository $sortieRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $inscription = new inscription();
        $inscription->setSortieId($sortieRepository->find($sortie->getId()));
        $inscription->setUserId($userRepository->find($user->getId()));
        $inscription->setDateInscription(new \DateTime());
        $entityManager->persist($inscription);
        $entityManager->flush();

        return $this->redirectToRoute('_list');
    }

    #[Route('/publier/{sortie}', name: '_publier', methods: ['GET'])]
    public function publication(Sortie $sortie, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {

        $sortie->setEtat($etatRepository->find(2));

        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('_list');
    }

    #[Route('/desiter/{sortie}', name: '_desister', methods: ['GET'])]
    public function deinscription( InscriptionRepository $inscriptionRepository, EntityManagerInterface $entityManager): Response
    {
            $inscription = $inscriptionRepository->findOneBy(['user_id' => $this->getUser()->getId()]);
        $user= $this->getUser();
        $user->removeInscription($inscription);
        $entityManager->remove($inscription);
        $entityManager->persist($user);
        $entityManager->flush();


        return $this->redirectToRoute('_list');
    }
}

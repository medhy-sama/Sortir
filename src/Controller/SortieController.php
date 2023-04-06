<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\rechercheSortie;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\AnnulerSortieType;
use App\Form\SearchType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;


#[AsEventListener(Etat::class)]
#[IsGranted("ROLE_USER")]
#[Route('/sortie')]
class SortieController extends AbstractController


/*
 * Renvoie sur la page qui liste les sorties
 * Utilisation du formulaire de filtre et
 * création d'un nouvel objet rechercheSortie pret à accueillir les infos demandées par l'utilisateur
 * la méthode search est passée dans le render afin d'avoir la liste de sorties à jour avec les case préalablement cochées
 * la méthode search prend en paramètre le user en cours, l'objet état en libelle passé et l'objet rechercheSortie**/
{
    #[Route('/', name: '_list', methods: ['GET', 'POST'])]
    public function listeSortie(
        SortieRepository $sortieRepository,
        EtatRepository   $etatRepository,
        Request          $request,
    ): Response
    {
        if ($this->getUser()->isActif() == false){
            $this->addFlash('error', 'Votre compte est inactif, veuillez contacter l\'administrateur');
            return $this->redirectToRoute('login_login');
        }
        $etatpasse = $etatRepository->find(5);
        $rechercheSortie = new rechercheSortie();
        $user = $this->getUser();
        $form = $this->createForm(SearchType::class, $rechercheSortie);
        $form->handleRequest($request);

        return $this->render('sortie/liste.html.twig', [
            'form' => $form,
            'sorties' => $sortieRepository->search($rechercheSortie, $user, $etatpasse)

        ]);
    }

    #[Route('/creer', name: '_creer', methods: ['GET', 'POST'])]
    public function creerSortie(
        Request                $request,
        EntityManagerInterface $em,
        EtatRepository         $etatRepository,
        CampusRepository       $campusRepository,
    ): Response

    {
        $sortie = new Sortie();

        $sortie->setCampus($campusRepository->find($this->getUser()->getCampus()->getId()));
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $sortie->setOrganisateur($this->getUser());
                $sortie->setEtat($etatRepository->find(1));
                $em->persist($sortie);
                $em->flush();
                $this->addFlash('succes', 'Votre sortie a été enregistré');
                return $this->redirectToRoute('_list', [], Response::HTTP_SEE_OTHER);
            }
        } catch (\Exception $exception) {
            $this->addFlash('error', 'Votre sortie n\'a pas été enregistré');
            return $this->redirectToRoute('_creer');
        }
        return $this->render('sortie/creer.html.twig', compact('sortie', 'form')
        );
    }

    #[Route('/{sortie}', name: '_detail', methods: ['GET'])]
    public function show(
        Sortie $sortie
    ): Response
    {
        $inscriptions = $sortie->getInscriptions();
        return $this->render('sortie/detail.html.twig',
            compact('sortie', 'inscriptions'));
    }

    #[Route('/edit/{id}', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request          $request,
        Sortie           $sortie,
        SortieRepository $sortieRepository
    ): Response
    {
        $datededbut = $sortie->getDatedebut();
        $datedujour = new \DateTime();
        if ($datededbut > $datedujour) {
            $form = $this->createForm(SortieType::class, $sortie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $sortieRepository->save($sortie, true);
                $this->addFlash('success', 'Vous avez bien modifié(e) la sortie');
                return $this->redirectToRoute('_list', [], Response::HTTP_SEE_OTHER);
            }
        } elseif ($datededbut < $datedujour) {
            $this->addFlash('error', 'Vous ne pouvez pas modifié(e) la sortie car la sortie à déjà débutée');
            return $this->redirectToRoute('_list', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(
        Request          $request,
        Sortie           $sortie,
        SortieRepository $sortieRepository
    ): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }
        return $this->redirectToRoute('_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/inscrire/{sortie}', name: '_inscrire', methods: ['GET'])]
    public function inscription(
        Sortie                 $sortie,
        EntityManagerInterface $entityManager
    ): Response
    {
        $datedujour = new \DateTime();
        $etat = $sortie->getEtat();
        $nbInscriptionMax = $sortie->getNbinscriptionsmax();
        $nbInscrit = count($sortie->getInscriptions());
        $complet = $nbInscriptionMax - $nbInscrit;
        if ($etat->getLibelle() == "Ouverte" and $complet > 0) {
            $user = $this->getUser();
            $inscription = new Inscription();
            $inscription->setSortieId($sortie);
            $inscription->setUserId($user);
            $inscription->setDateInscription($datedujour);
            $entityManager->persist($inscription);
            $entityManager->flush();
            $this->addFlash('success', 'Vous vous étes bien inscrit(e)');

            return $this->redirectToRoute('_list');
        } else {
            $this->addFlash('error', 'Vous n\'avez pas été inscrit(e), les inscriptions sont cloturées ou complètes');
            return $this->redirectToRoute('_list');
        }
    }

    #[Route('/publier/{sortie}', name: '_publier', methods: ['GET'])]
    public function publication(
        Sortie                 $sortie,
        EtatRepository         $etatRepository,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $datededbut = $sortie->getDatedebut();
        $datedujour = new \DateTime();

        if ($datededbut > $datedujour) {
            $sortie->setEtat($etatRepository->find(2));
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez publiez la sortie');
            return $this->redirectToRoute('_inscrire', [
                'sortie' => $sortie->getId()
            ]);
        } else {
            $this->addFlash('error', 'Vous ne pouvez pas publier la sortie, la date du début de la sortie est antérieur à la date du jour.<br>' . 'Veuillez modifier la date du début de la sortie');
            return $this->redirectToRoute('_list');
        }
    }

    #[Route('/desiter/{sortie}', name: '_desister', methods: ['GET'])]
    public function deinscription(
        InscriptionRepository  $inscriptionRepository,
        EntityManagerInterface $entityManager,
        Sortie                 $sortie
    ): Response
    {
        $etat = $sortie->getEtat();
        if ($etat->getLibelle() == "Ouverte" or $etat->getLibelle() == "Cloturee") {
            $inscription = $inscriptionRepository->findOneBy(['user_id' => $this->getUser()->getId()]);
            $user = $this->getUser();
            $user->removeInscription($inscription);
            $entityManager->remove($inscription);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Vous vous étes bien désisté(e)');


            return $this->redirectToRoute('_list');
        } else {
            $this->addFlash('error', 'Vous n\'avez pas été désinscrit(e), la sortie a débutée ');
            return $this->redirectToRoute('_list');
        }
    }

    #[Route('/annuler/{sortie}', name: '_motif_annuler')]
    public function motif_annulation(
        Sortie                 $sortie,
        EntityManagerInterface $entityManager,
        Request                $request,
        EtatRepository         $etatRepository
    ): Response
    {
        $form = $this->createForm(AnnulerSortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setMotif($request->request->get('motif'));
            $entityManager->persist($sortie);
            $entityManager->flush();
            $datededbut = $sortie->getDatedebut();
            $datedujour = new \DateTime();
            $etat = $sortie->getEtat()->getId();
            if ($datededbut > $datedujour) {
                if ($etat == 2 or $etat == 3) {
                    $sortie->setEtat($etatRepository->find(6));
                    $entityManager->persist($sortie);
                    $entityManager->flush();
                    $this->addFlash('success', 'Vous avez annuler la sortie');
                    return $this->redirectToRoute('_list');
                }
            }
        }
        return $this->render('sortie/annuler.html.twig', compact('form'));
    }


    /**
     * @param Ville $ville
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param LieuRepository $lieuRepository
     * @return JsonResponse
     * Route pour qui permet de faire une requete pour récupérer les lieux correspondant à une ville
     * Retourne un JsonResponse
     */
    #[Route('/lieu-city/{ville}', name: '_lieu', methods: ['GET'])]
    public function listLieuDeVille(
        Ville                  $ville,
        Request                $request,
        EntityManagerInterface $em,
        LieuRepository         $lieuRepository
    ): JsonResponse
    {

        $em = $this->getUser();

        $lieux = $lieuRepository->createQueryBuilder("q")
            ->where("q.ville = :villeId")
            ->setParameter("villeId", $ville->getId())
            ->getQuery()
            ->getResult();

        $responseArray = array();
        foreach ($lieux as $lieu) {
            $responseArray[] = array(
                "id" => $lieu->getId(),
                "nom_lieu" => $lieu->getNomLieu()
            );
        }
        return new JsonResponse($responseArray);
    }
}

<?php

namespace App\Controller;

use App\Controller\Admin\UserCrudController;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\String\Slugger\SluggerInterface;


class MainController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/compte', name: '_afficher')]
    public function afficher(): Response
    {
        return $this->render('main/compte.html.twig');
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/compte/{id}',
        name: '_profil_inscrit',
        requirements: ['id' => '\d+']
    )]
    public function afficher_profil(
        User $user,
    ): Response
    {
        $message = '';

        return $this->render('main/profil_inscrit.html.twig', compact('message', 'user')
        );
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/compte/modifier/{id}', name: '_modifier', requirements: ['id' => '\d+'])]
    public function modifier(
        User                   $user,
        EntityManagerInterface $entityManager,
        Request                $request,
        User                   $id,
        UserRepository         $userRepository,
        SluggerInterface       $slugger
    ): Response
    {
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        $val1 = $userRepository->find($id);
        if ($userForm->isSubmitted() and $userForm->isValid()) {
            $photo = $userForm->get('photo')->getData();
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();
                try {
                    $photo->move(
                        $this->getParameter('user_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $id->setPhoto($newFilename);
            }

            $val2 = password_verify($userForm->get('password')->getData(), $val1->getPassword());
            if (($val1->getPassword()) == $val2) {
                $entityManager->persist($id);
                $entityManager->flush();
                $this->addFlash('success', 'Vous avez bien modifié vos informations');

                return $this->render('main/compte.html.twig');
            }
            $this->addFlash('success', 'Votre mot de passe n\'est pas bien renseigné');

            return $this->render('main/modifier.html.twig', compact('userForm'));
        }

        return $this->render('main/modifier.html.twig', compact('userForm'));
    }

}

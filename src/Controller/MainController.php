<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/compte', name: '_afficher')]
    public function afficher(): Response
    {
        return $this->render('main/compte.html.twig'
        );
    }


    #[Route('/compte/modifier/{id}', name: '_modifier', requirements: ['id' => '\d+'])]
    public function modifier(User $user, EntityManagerInterface $entityManager,Request $request, User $id, UserRepository $userRepository): Response
    {
        $userForm =$this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        $val1 = $userRepository->find($id);
        if ($userForm->isSubmitted() and $userForm->isValid()){
            if ($val1->getPassword() != $userForm->get('password')){
dd($userForm->get('password'));
                $entityManager->persist($id);
                $entityManager->flush();
                return $this->render('main/compte.html.twig');
            }
            }

        return $this->render('main/modifier.html.twig',compact('userForm'));
    }

}

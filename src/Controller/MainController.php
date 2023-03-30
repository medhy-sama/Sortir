<?php

namespace App\Controller;

use App\Controller\Admin\UserCrudController;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


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
        $message='';
        return $this->render('main/compte.html.twig', compact('message')
        );
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/compte/modifier/{id}', name: '_modifier', requirements: ['id' => '\d+'])]
    public function modifier(User $user, EntityManagerInterface $entityManager,Request $request, User $id, UserRepository $userRepository): Response
    {
        $userForm =$this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        $val1 = $userRepository->find($id);
        if ($userForm->isSubmitted() and $userForm->isValid()){
            $val2=password_verify($userForm->get('password')->getData(),$val1->getPassword()) ;
            if (($val1->getPassword()) == $val2){
                dump($val2);
                dump($val1);
                $entityManager->persist($id);
                $entityManager->flush();
                $message = "Bravo vous avez modifié vos informations !!!";
                return $this->render('main/compte.html.twig', compact('message'));
            }
            $message = "Votre mot de passe n'est pas bien renseigné !!!";
            return $this->render('main/modifier.html.twig',compact('userForm', 'message'));
            }
$message='';
        return $this->render('main/modifier.html.twig',compact('userForm', 'message'));
    }




}

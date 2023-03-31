<?php

namespace App\Controller\Admin;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class adminController extends AbstractDashboardController
{

    #[Route('/admin', name: 'admin')]
    #[isGranted('ROLE_ADMIN')]
    public function index(): Response
    {
//        return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
         return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Sortir');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fas fa-landmark');
        yield MenuItem::linkToCrud('Stagiaires', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Sorties', 'fas fa-glass-martini-alt', Sortie::class);
        yield MenuItem::linkToCrud('Campus', 'fas fa-building', Campus::class);
        yield MenuItem::linkToCrud('Villes', 'fas fa-globe-europe', Ville::class);
        yield MenuItem::linkToCrud('Lieu', 'fas fa-map-marked-alt', Lieu::class);
        yield MenuItem::linkToRoute('Home', 'fas fa-home', '_index');
    }
}

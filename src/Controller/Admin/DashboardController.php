<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use App\Entity\Configuration;
use App\Entity\Covoiturage;
use App\Entity\Marque;
use App\Entity\Parametre;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Voiture;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');


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
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Ecoride');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Marque', 'fas fa-list', Marque::class);
        yield MenuItem::linkToCrud('Role', 'fa-solid fa-people-group', Role::class);
        yield MenuItem::linkToCrud('Avis', 'fa-solid fa-comments', Avis::class);
        yield MenuItem::linkToCrud('Covoiturage', 'fa-solid fa-key', Covoiturage::class);
        yield MenuItem::linkToCrud('User', 'fa-solid fa-user', User::class);
        yield MenuItem::linkToCrud('Voiture', 'fa-solid fa-car', Voiture::class);
        yield MenuItem::linkToCrud('Configuration', 'fa-solid fa-screwdriver-wrench', Configuration::class);
        yield MenuItem::linkToCrud('Parametre', 'fa-solid fa-gear', Parametre::class);
    }
}

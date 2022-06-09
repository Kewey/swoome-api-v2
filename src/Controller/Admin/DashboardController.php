<?php

namespace App\Controller\Admin;

use App\Entity\Expense;
use App\Entity\ExpenseType;
use App\Entity\Group;
use App\Entity\GroupType;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();

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
        return $this->render('/dashboard/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img class="pe-3" src="./logos/logo.png">');
    }

    public function configureMenuItems(): iterable
    {

        return [
            MenuItem::linkToDashboard('Accueil', 'fa fa-home'),
            MenuItem::linkToCrud('Catégories Groupe', 'fa-solid fa-people-roof', GroupType::class),
            MenuItem::linkToCrud('Catégories Dépense', 'fa-solid fa-tag', ExpenseType::class),
            MenuItem::linkToCrud('Dépenses', 'fas fa-money-bill-transfer', Expense::class),
            MenuItem::linkToCrud('Groupes', 'fas fa-user-group', Group::class),
            MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class),
        ];
    }
}

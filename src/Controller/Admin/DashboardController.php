<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use App\Entity\Category;
use App\Entity\Header;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        //return parent::index();

        //Permet de générer les liens dans l'interface d'administration
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);

        //Créer l'interface d'admin pour les users
        $url = $routeBuilder->setController(UserCrudController::class)->generateUrl();

        return $this->redirect($url);

         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

         return $this->redirect($adminUrlGenerator->setController(OrderCrudController::class)->generateUrl());

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Laboutiquecomplete');
    }

    public function configureMenuItems(): iterable
    {
        //yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::linktoRoute('Back to the website', 'fas fa-home', 'home'); //Lien pour revenir à la page home de notre site

        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class); //Admin des Users

        yield MenuItem::linkToCrud('Categories', 'fa fa-list-alt ', Category::class); // Admin des Categories

        yield MenuItem::linkToCrud('Products', 'fa fa-tag ', Product::class); // Admin des Categories

        yield MenuItem::linkToCrud('Carriers', 'fa fa-truck ', Carrier::class); // Admin des Carriers

        yield MenuItem::linkToCrud('Orders', 'fa fa-shopping-cart ', Order::class); // Admin Orders

        yield MenuItem::linkToCrud('Headers', 'fa fa-desktop ', Header::class); // Admin Orders
    }
}

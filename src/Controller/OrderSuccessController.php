<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/success/{stripeSessionId}', name: 'order_success')]
    public function index(Cart $cart,$stripeSessionId): Response
    {
       $order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);

       //Si la commande n'existe pas ou si l'utilisateur de la commande n'est pas l'utilisateur courant =
       if(!$order || $order->getUser() != $this->getUser()){
           return $this->redirectToRoute('home');
       }

       //On test bien sûr si la commande n'a pas encore été payé avant de changer son statut
       if(!$order->getIsPaid()){
           //Vider la session cart
           $cart->remove();
           //Modifier le statut isPaid de notre commande en passant le statut à 1
           $order->setIsPaid(1);
           $this->entityManager->flush(); //On valide le changement en base de données

           //Envoyer un mail à notre client pour lui confirmer sa commande
       }
        //Afficher les quelques informations de la commande utilisateur



        return $this->render('order_success/index.html.twig',[
            'order' => $order
        ] );
    }
}

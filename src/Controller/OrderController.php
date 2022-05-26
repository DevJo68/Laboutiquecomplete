<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{

    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface  $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande', name: 'order', methods:['GET','POST'])]
    public function index(Cart $cart): Response
    {
        //si l'utilisateur courant n'a pas d'adresses enregistrés (le tableau getValues vide)
        if(!$this->getUser()->getAddresses()->getValues()){
            return $this->redirectToRoute('account_address_add'); //On le redirige vers la page pour en ajouter
        }
        //Sinon on continue le reste du processus
        $form = $this->createForm(OrderType::class,null,[
            'user' => $this->getUser() //On récupère l'utilisateur courant
        ]);

        return $this->render('order/index.html.twig',[
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'order_recap')]
    public function add(Cart $cart, Request $request): Response
    {

        $form = $this->createForm(OrderType::class,null,[
            'user' => $this->getUser() //On récupère l'utilisateur courant
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $date = new \DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname().''.$delivery->getLastname();
            $delivery_content .=  '<br/>'.$delivery->getPhone();
            if($delivery->getCompany()){
                $delivery_content .= '<br/>'.$delivery->getCompany();
            }

            $delivery_content .= '<br/>'.$delivery->getAddress();
            $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content .= '<br/>'.$delivery->getCountry();


            //Enregistrer ma commande Order
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setisPaid(0);

            //On persiste l'order pour l'intégrer en base de données
            $this->entityManager->persist($order);


            // Enregistrer mes produits OrderDetails
            foreach  ($cart->getFull() as $product){
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setMyOrder($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                //On persiste chaque orderDetails pour les ajouter en base de données
                $this->entityManager->persist($orderDetails);
            }

            // Le flush à la fin pour valider toutes les transactions en base de données
            $this->entityManager->flush();

            //On va à l'etape suivante du canal d'achat uniquement si on a soumis le formulaire
            return $this->render('order/add.html.twig',[
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content
            ]);
        }

        return $this->redirectToRoute('cart');

    }
}

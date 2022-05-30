<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @throws \Stripe\Exception\ApiErrorException
     */
    #[Route('/commande/create_session/{reference}', name: 'stripe_create_session')]
    public function index(EntityManagerInterface $entityManager,$reference): RedirectResponse
    {
        $product_for_stripe = [];
        $YOUR_DOMAIN = 'http://localhost:8000';

        $order = $entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference ]); //On récupère la commande à l'aide la référence passer en paramètre

        if(!$order){
            //Redirection vers la page d'erreur
        }

        //On remplit le tableau stripe pour le paiement

        $stripe = new \Stripe\StripeClient('sk_test_51L3cyKHffSXaoux5JSvYwwyLyLyVuFqyH2OQaOtiN69iEY578ptIhQLMdCGlBPZB16F5iss966ISpFxG4BwbvlAb00uJCTNd1U');

        foreach  ($order->getOrderDetails()->getValues() as $product)
        {
            $product_object = $entityManager->getRepository(Product::class)->findOneBy(['name' => $product->getProduct()]);
            //Créer un nouveau produit dans stripe qui sera identifié
            $stripe->products->create([
                    'name' =>  $product_object->getName(),
                     'description' =>  $product_object->getDescription(),
                     'images' => [],
                     'default_price_data' => ['unit_amount' => $product_object->getPrice(), 'currency' => 'eur'], //Creation de la partie prix du produit
                     'expand' => ['default_price'],
            ]);

            //On créer un tableau avec les infos concernant les produits créer dans stripe pour la ligne line_items pour le checkout avant paiement
            $stripe_products[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' =>  $product_object->getName(),
                        'description' => $product_object->getDescription(),
                        'images' => []
                    ],
                    'unit_amount' => $product_object->getPrice()
                ],
                 'quantity' => $product->getQuantity(),
            ];


        }
        $stripe_products[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice() * 100,
                'product_data' => [
                    'name' =>  $order->getCarrierName(),
                ],
            ],
            'quantity' => 1,
        ];


        Stripe::setApiKey('sk_test_51L3cyKHffSXaoux5JSvYwwyLyLyVuFqyH2OQaOtiN69iEY578ptIhQLMdCGlBPZB16F5iss966ISpFxG4BwbvlAb00uJCTNd1U');

        header('Content-Type: application/json');

            $checkout_session = Session::create([
                'customer_email' => $this->getUser()->getEmail(),
                'payment_method_types' => ['card'],
                'line_items' => [
                    $stripe_products
                ],
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/commande/success/{CHECKOUT_SESSION_ID}',
                'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
            ]);

            $order->setStripeSessionId($checkout_session->id);
            $entityManager->flush(); //On valide le fait de rajouter en base de données notre attribut stripeSessionsId

        return new RedirectResponse($checkout_session->url);
    }
}

<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private SessionInterface $session;
    private EntityManagerInterface $entityManager;

    /**
     * @param SessionInterface $session
     */
    public function __construct(EntityManagerInterface $entityManager,SessionInterface $session)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function add($id)
    {
        $cart = $this->session->get('cart',[]);

        //si le panier n'est pas vide
        if(!empty($cart[$id])){
            $cart[$id]++; //On ajoute une quantité au produit qui est déjà dedans
        }else{
            $cart[$id] = 1; //Sinon on initialise lq quantité pour le produit en question
        }

        $this->session->set('cart',$cart);

    }

    public function get(){
        return $this->session->get('cart');
    }

    public function remove(){
        $this->session->remove('cart');
    }

    public function delete($id){
        $cart = $this->session->get('cart',[]);

        unset($cart[$id]); //On supprime l'élément du panier avec l'id concerner

        return $this->session->set('cart',$cart); //Un fois la suppression effectué, on resaisie le nouveau clavier dans la session avec du coup un article en moins
    }

    public function decrease($id){
        //On récupère la panier en cours dans la session
        $cart = $this->session->get('cart',[]);
        //vérifier que la quantité du produit dans le panier est supérieur à 1
        if($cart[$id] > 1){
            $cart[$id]--;
        }else{
            unset($cart[$id]); //On supprime l'élément du panier avec l'id concerner
        }
        return $this->session->set('cart',$cart); //Un fois la suppression effectué, on resaisie le nouveau clavier dans la session a
    }

    public function getFull()
    {
        $cartComplete = []; //Variable qui va contenir les produits de notre panier


        if($this->get()){
                //Ici on récupère le objet panier($cart) (un tableau clé($id) => valeur($quantity)) et on itère dessus
                foreach ($this->get() as $id => $quantity) {

                       $product_object =  $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);//On récupère le produit associer à l'id via l'entity manager

                       //Si le produit n'existe pas alors on le supprime
                       if(!$product_object){
                         $this->delete($id);
                         continue; //On passe au produit suivant pour éviter le erreurs
                       }

                        $cartComplete[] = [
                            'product' =>  $product_object,
                            'quantity' =>    $quantity

                        ];
                  }
            }
            return $cartComplete;
    }

}
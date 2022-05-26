<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/adresses', name: 'account_address')]
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }

    #[Route('/compte/ajouter-adresse', name: 'account_address_add')]
    public function add(Cart $cart,Request $request): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class,$address);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser()); // On saisie dans l'objet addresss le user associé à celle-ci
            $this->entityManager->persist($address); //On fige et prépare la donnée pour qu'elle soit insérer en base de données
            $this->entityManager->flush(); //Et on valide la commande en base de données

            if($cart->get()){
                return $this->redirectToRoute('order');
            }else{
                return $this->redirectToRoute('account_address');
            }

        }

        return $this->render('account/address_add.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/compte/modifer-une-adresse/{id}', name: 'account_address_edit')]
    public function edit(Request $request,$id): Response
    {

        $address = $this->entityManager->getRepository(Address::class)->findOneBy(['id' => $id]);

        //Si l'adresse à l'id indiquer dans l'url n'existe pas ou si ce n'est pas l'adresse de l'utilisateur actuellement connecté
        if(!$address || $address->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_address'); //On redirige l'utilisatreur sur la page principal compte/adresse
        }

        $form = $this->createForm(AddressType::class,$address);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->flush(); //Et on valide la commande en base de données
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_add.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/compte/supprimer-une-adresse/{id}', name: 'account_address_delete')]
    public function delete($id): Response
    {

        $address = $this->entityManager->getRepository(Address::class)->findOneBy(['id' => $id]);

        //Si l'adresse à l'id indiquer dans l'url n'existe pas ou si ce n'est pas l'adresse de l'utilisateur actuellement connecté
        if($address || $address->getUser()  == $this->getUser()){
            $this->entityManager->remove($address);
            $this->entityManager->flush();
        }


        return $this->redirectToRoute('account_address');

    }
}

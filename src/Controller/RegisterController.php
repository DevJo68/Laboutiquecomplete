<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class RegisterController extends AbstractController
{

    private $entityManager;

    //On récupère doctrine dans le constructeur afin de pouvoir travailler sur notre base de données
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'register')]
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User(); //On instancie un objet de la classe User
        $form = $this->createForm(RegisterType::class, $user); // On crée un formulaire basé sur le modèle de la classe user

        $form->handleRequest($request); //Cette ligne permet à notre formulaire d'écouter la requête post lors de la validation du formulaire

        //On test si le formulaire a été soumis et si il est valid
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData(); //On récupère les valeurs saisies dans le forumulaire

            //Encodage du password
            $password = $user->getPassword();
            $encoded = $encoder->encodePassword($user, $password);
            $user->setPassword($encoded);

            //dd($user); //dd est un var_dump

            $this->entityManager->persist($user); //On prépare la transaction pour la base de données
            $this->entityManager->flush(); //On effectue la transaction vers la base de données

        }
        //On retourne  le formulaire nommer 'form', afin de pouvoir l'utiliser dans la vue twig associé au formulaire
        return $this->render('register/index.html.twig',[
            'form' => $form->createView() //On crée la vue du formulaire
        ]);
    }
}

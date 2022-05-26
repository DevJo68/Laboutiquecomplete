<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountPasswordController extends AbstractController
{
    private $entityManager;

    /**
     * @param $entityManager
     */
    public function __construct( EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/compte/modifier_motdepasse', name: 'account_password')]
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;

        $user = $this->getUser(); //On récupère les données de l'utilisateur connecté  à l'aide de app
        $form = $this->createForm(ChangePasswordType::class,$user); //Creation du formulaire basé toujours sur le modèle User

        $form->handleRequest($request); //Cette ligne permet à notre formulaire d'écouter la requête post lors de la validation du formulaire

        //On test si le formulaire a été soumis et si il est valid
        if ($form->isSubmitted() && $form->isValid()) {

            $old_pwd = $form->get('old_password')->getData(); //On récupère la valeur saisie dans le champ old_password

            //On test si le mot de passe saisie correspond bien à celui de l'utilisateur connecté
            if($encoder->isPasswordValid($user,$old_pwd)){
                //On récupère le nouveau mot de passe saisie dans le champ new_password
                $new_pwd = $form->get('new_password');
                //On encode le nouveau mot de passe avant de l'enregistrer dans la table user
                $password = $encoder->encodePassword($user,$new_pwd->getData());
                //On saisir le nouveau mot de passe pour le user concerné
                $user->setPassword($password);
                //On effectue la transaction vers la base de données
                $this->entityManager->flush();
                $notification = 'Votre mot de passe a bien été mis à jour ';
            }else{
                $notification = 'Votre mot de passe n`est pas le bon ';
            }



        }

        return $this->render('account/password.html.twig',[
         'form' => $form->createView(), //On crée la vue du formulaire
         'notification' => $notification
        ]);
    }
}

<?php

namespace App\Form;

use App\Classe\Search;

use App\Entity\Category;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('string',TextType::class,[
                   'label' => 'Rechercher',
                   'required' => false,
                   'attr' =>[
                       'placeholder' => 'Entrer une catégorie',
                       'class' => 'form-control-sm'
                   ]
            ])
            ->add('categories', EntityType::class,[
                'label' => false,
                'required' => false,
                'class' => Category::class, //La classe sur laquelle sera basé nos résultats
                'multiple' => true, //Ici on spécifie que l'on veut pouvoir séléctionner plusieurs valeurs dans le champ
                'expanded' => true //Permet d'avoir une vue en checkbox pour la séléction multiple

            ])
            ->add('submit',SubmitType::class,[
                'label' => 'Filtrer',
                'attr' =>[
                    'class' => 'btn btn-info btn-block'
                ]
            ])
        ;
    }

    //Ici on va lier notre formulaire à la data_class Search::class qui représente notre recherche
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'crsf_protection' => false
        ]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return ''; // TODO: Change the autogenerated stub
    }

}
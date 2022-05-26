<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'label' => 'Quelle nom souhaitez-vous donner à cette adresse',
                'attr'  => [
                    'placeholder' => 'Nommez votre addresse'
                ]
            ])
            ->add('firstname',TextType::class,[
                'label' => 'Votre prenom',
                'attr'  => [
                    'placeholder' => 'Merci de saisir votre prenom'
                ]
            ])
            ->add('lastname',TextType::class,[
                'label' => 'Votre nom',
                'attr'  => [
                    'placeholder' => 'Merci de saisir votre nom'
                ]
            ])
            ->add('address',TextType::class,[
                'label' => 'Votre adresse',
                'attr'  => [
                    'placeholder' => 'Merci de saisir votre address'
                ]
            ])
            ->add('postal',TextType::class,[
                'label' => 'Votre code postal',
                'attr'  => [
                    'placeholder' => 'Merci de saisir votre code postal'
                ]
            ])
            ->add('city',TextType::class,[
                'label' => 'Votre ville',
                'attr'  => [
                    'placeholder' => 'Merci de saisir votre  ville'
                ]
            ])
            ->add('country',CountryType::class,[
                'label' => 'Votre pays',
                'attr'  => [
                    'placeholder' => 'Merci de saisir votre pays'
                ]
            ])
            ->add('phone',TelType::class,[
                'label' => 'Votre téléphone',
                'attr'  => [
                    'placeholder' => 'Merci de saisir votre téléphone'
                ]
            ])
            ->add('company',TextType::class,[
                'label' => 'Votre société',
                'required' => false,
                'attr'  => [
                    'placeholder' => ' (Facultatif) Merci de saisir votre société'
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Ajouter une adresse',
                'attr' => [
                    'class' => 'btn-block btn-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}

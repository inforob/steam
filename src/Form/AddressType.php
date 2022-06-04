<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address',TextType::class, [
                'attr'=> ['autocomplete' => 'off', 'class' => 'form-control col-lg-6' ]
            ])
            ->add('cp',TextType::class, [
                        'attr'=> [
                            'autocomplete' => 'off',
                            'class' => 'form-control col-lg-1' ,
                            'maxlength' => 5
                        ]
            ])
            ->add('country',TextType::class, [
                'attr'=> ['autocomplete' => 'off', 'class' => 'form-control col-lg-2' ]
            ])
            ->add('state',TextType::class, [
                'attr'=> ['autocomplete' => 'off', 'class' => 'form-control col-lg-2' ]
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

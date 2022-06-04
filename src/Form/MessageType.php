<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class, [
                    'attr'=> ['autocomplete' => 'off', 'class' => 'form-control col-lg-6' ]
            ])
            ->add('name',TextType::class, [
                'attr'=> ['autocomplete' => 'off', 'class' => 'form-control col-lg-6' ]
            ])
            ->add('text',TextareaType::class, [
                'attr'=> ['autocomplete' => 'off', 'class' => 'form-control col-lg-6' ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}

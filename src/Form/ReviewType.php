<?php

namespace App\Form;

use App\Entity\Review;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('rating', HiddenType::class,[
                'data' => Review::MIN_VALUE_RATING,
            ])
            ->add('comment', TextareaType::class,[
                'attr' => [
                    'class' => 'form-control required']
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {

                $reviewFormData = $event->getData();
                $ratePercentage = intval($reviewFormData['rating']) * 100 / Review::MAX_VALUE_RATING;
                $reviewFormData['rating'] = $ratePercentage;
                $event->setData($reviewFormData);
            });
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}

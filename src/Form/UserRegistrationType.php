<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegistrationType extends AbstractType
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr'=> ['autocomplete' => 'off', 'class' => 'form-control' ]
            ])
            ->add('password', PasswordType::class,[
                'attr'=> ['autocomplete' => 'off', 'class' => 'form-control' ]
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                /** @var User $user */
                $userFormData = $event->getData();
                $user = $event->getForm()->getData();

                if (!$userFormData) {
                    return;
                }

                $encodedPassword = $this->passwordHasher->hashPassword(
                    $user, $userFormData['password']
                );
                $userFormData['password'] = $encodedPassword;

                $event->setData($userFormData);
            });
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

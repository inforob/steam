<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserResetPasswordType extends AbstractType
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('password', TextType::class, [
                'mapped' => false,
                'attr'=> ['autocomplete' => 'off', 'class' => 'form-control' ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add(
                'new_password', TextType::class, [
                'mapped' => false,
                'attr'=> ['autocomplete' => 'off', 'class' => 'form-control' ],
                'constraints' => [
                    new NotBlank(),
                    new Length(['max'=>10]),
                ],
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use($options){

                $userFormData = $event->getData();

                /** @var User $user */
                $user = $options['data'];
                $encodedPassword = $this->passwordHasher->hashPassword(
                    $user, $userFormData['password']
                );
                $user->setPassword($encodedPassword) ;
                $user->setToken(null);


            })->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use($options){

                $form = $event->getForm();
                if($form->get('password')->getData() !== $form->get('new_password')->getData()){
                    $form->get('password')->addError(new FormError('the password is not identicals'));
                }

            });
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}

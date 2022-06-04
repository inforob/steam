<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\User;
use App\Form\AddressType;
use App\Form\UserProfileType;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile", name="app")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="_profile", methods={"GET","POST"} )
     */
    public function profile(Request $request,UserRepository $userRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $userRepository->add($user,true);
            $this->addFlash(
                'success',
                'Updated user ' . $user->getUserIdentifier()
            );
        }

        $address = $user->getAddress();
        $formAddress=$this->createForm(AddressType::class, $address);
        $formAddress->handleRequest($request);
        if($formAddress->isSubmitted() && $formAddress->isValid()){
            $userRepository->add($user->setAddress($formAddress->getData()),true);
            $this->addFlash(
                'success',
                'Updated address for user ' . $user->getUserIdentifier()
            );
        }
        return $this->render('user/profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'form' => $form->createView(),
            'formAddress' => $formAddress->createView()
        ]);
    }
}

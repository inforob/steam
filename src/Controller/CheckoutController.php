<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/checkout", name="app_checkout")
     */
    public function checkout(Request $request,UserRepository $userRepository): Response
    {
        if('POST' === $request->getMethod()){
            $dataPost = $request->request->all();

            $userUpdated = $this->updateDataUser($dataPost);

            $userRepository->add($userUpdated,true);
            // TODO persist ORDER
            // TODO send email with events
            $this->addFlash(
                'success',
                'Order registered successfully, you must receive an email with your order!, Thanks'
            );
            return $this->redirectToRoute('app_home');
        }

        return $this->render('checkout/checkout.html.twig', [

        ]);
    }

    private function updateDataUser(array $dataPost) : User
    {
        /** @var User $user */
        $user = $this->getUser();

        if(null === $user->getName()) {
            $user->setName($dataPost['fname'] ?? "N/A");
        }
        if(null === $user->getLastname()){
            $user->setLastname($dataPost['lname'] ?? "N/A");
        }
        if(null === $user->getPhone() ){
            $user->setPhone($dataPost['phone'] ?? "N/A");
        }
        if(null === $user->getAddress()->getAddress() ){
            $user->getAddress()->setAddress($dataPost['address'] ?? "N/A");
        }
        if(null === $user->getAddress()->getState() ){
            $user->getAddress()->setState($dataPost['state'] ?? "N/A");
        }
        if(null === $user->getAddress()->getCountry() ){
            $user->getAddress()->setCountry($dataPost['country'] ?? "N/A");
        }
        if(null === $user->getAddress()->getCp() ){
            $user->getAddress()->setCp($dataPost['zip'] ?? "N/A");
        }

        return $user;

    }
}

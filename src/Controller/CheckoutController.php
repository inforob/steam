<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\LineItem;
use App\Entity\LineItems;
use App\Entity\Order;
use App\Entity\User;
use App\Events\OrderEvent;
use App\Events\UserEvent;
use App\Repository\GameRepository;
use App\Repository\LineItemRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    private SessionInterface $session;
    private EventDispatcherInterface $dispatcher;

    public function __construct(SessionInterface $session,EventDispatcherInterface $dispatcher){

        $this->session = $session;
        $this->dispatcher = $dispatcher;
    }
    /**
     * @Route("/checkout", name="app_checkout")
     */
    public function checkout(
        Request $request,
        UserRepository $userRepository,
        OrderRepository $orderRepository,
        GameRepository $gameRepository
    ): Response
    {
        if('POST' === $request->getMethod()){

            $dataPost = $request->request->all();

            $userUpdated = $this->updateDataUser($dataPost);

            $userRepository->add($userUpdated,true);
            $itemsCart = $this->session->get('cartItems');

            $order = new Order();
            $order->setUser($userUpdated);
            $order->setStatus(Order::STATUS_PENDING);

            $sub_total = 0.00;
            foreach ($itemsCart as $itemId => $item) {
                /** @var Game $game */
                $game = $gameRepository->find($itemId);
                $line = new LineItems();
                $line->setQuantity($item['quantity']);
                $line->setGame($game);
                $order->addItem($line);

                $sub_total += $game->getPrice() * intval($item['quantity']);
            }

            $amount = number_format((Order::IVA * $sub_total / 100) + $sub_total,Order::DECIMALS);
            $order->setAmount($amount);
            $orderRepository->add($order,true);

            $this->dispatcher->dispatch(new OrderEvent($order,$userUpdated),OrderEvent::REGISTER_ORDER_ACTION);

            $this->addFlash(
                'success',
                'Order registered successfully, you must receive an email with your order!, Thanks'
            );
            $this->session->set('cartItems', []);
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

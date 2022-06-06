<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/cart", name="app_cart")
 */
class CartController extends AbstractController
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session){

        $this->session = $session;
    }

    /**
     * @Route("/add/{id}/{quantity}", name="_add_items", methods={"GET"} , options={"expose"=true} )
     * @ParamConverter("game", class="App\Entity\Game")
     */
    public function add(Game $game,$quantity): RedirectResponse
    {
        if(null == $this->session->get('cartItems')){
            $items = [];
        } else {
            $items = $this->session->get('cartItems');
        }

        $items[$game->getId()]=[
            'quantity' => $quantity,
            'game' => $game
        ];

        $this->session->set('cartItems', $items);

        return $this->redirectToRoute('app_cart_items');
    }

    /**
     * @Route("/update/{id}", name="_update_items", methods={"GET","POST"})
     * @ParamConverter("game", class="App\Entity\Game")
     */
    public function update(Request $request, Game $game): Response
    {
        $quantity = $request->query->get('cart')['quantity'];
        if(null === $quantity){
            throw new Exception('quantity required for the item');
        }

        $items = $this->session->get('cartItems');

        $items[$game->getId()]=[
            'quantity' => $quantity,
            'game' => $game
        ];

        $this->session->set('cartItems', $items);

        return $this->redirectToRoute('app_cart_items');
    }

    /**
     * @Route("/del/{idItem}", name="_del_items", methods={"GET"})
     */
    public function del($idItem): Response
    {
        $items = $this->session->get('cartItems');

        if(!isset($items[$idItem])){
            throw new Exception('the game not exist or has been removed from session');
        }

        unset($items[$idItem]);

        $this->session->set('cartItems', $items);

        return $this->redirectToRoute('app_cart_items');
    }
    /**
     * @Route("/", name="_items")
     */
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }

    private function emptyCart() : void
    {
        $this->session->set('cartItems', []);
    }

}

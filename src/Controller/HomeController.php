<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="_home")
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('home/index.html.twig',[
            'posts' => $postRepository->findBy(['published'=>Post::PUBLISHED])
        ]);
    }
}

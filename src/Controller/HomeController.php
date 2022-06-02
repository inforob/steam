<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/", name="app")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="_home")
     */
    public function index(PostRepository $postRepository, TopicRepository $topicRepository): Response
    {
        return $this->render('home/index.html.twig',[
            'posts' => $postRepository->findBy(['published'=>Post::PUBLISHED]),
            'topics' => $topicRepository->findAll()
        ]);
    }

    /**
     * @Route("/post/{slug}", name="_post")
     * @ParamConverter("post", class="App\Entity\Post")
     */
    public function post(PostRepository $postRepository,Post $post): Response
    {
        return $this->render('post/index.html.twig',[
            'posts' => $postRepository->findBy(['published'=>Post::PUBLISHED]),
            'post' => $post,
        ]);
    }
}

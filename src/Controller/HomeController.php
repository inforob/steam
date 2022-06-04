<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Message;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\MessageType;
use App\Repository\CommentRepository;
use App\Repository\MessageRepository;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(
        Request $request,
        PostRepository $postRepository,
        TopicRepository $topicRepository,
        MessageRepository $messageRepository
    ): Response
    {
        $contactForm = $this->createForm(MessageType::class, new Comment());
        $contactForm->handleRequest($request);

        if($contactForm->isSubmitted() && $contactForm->isValid())
        {
            /** @var User $user */
            $user = $this->getUser();
            /** @var Message $message */
            $message = $contactForm->getData();
            if( null === $message->getEmail()){
                $message->setEmail($user->getEmail());
            }

            $this->addFlash(
                'success',
                'comment added succesfully'
            );
            $messageRepository->add($message,true);

            return new RedirectResponse($this->generateUrl('app_home').'#contactForm');
        }

        return $this->render('home/index.html.twig',[
            'posts' => $postRepository->findBy(['published'=>Post::PUBLISHED]),
            'topics' => $topicRepository->findAll()
        ]);
    }

    /**
     * @Route("/post/{slug}", name="_post" , methods={"GET","POST"})
     * @ParamConverter("post", class="App\Entity\Post")
     * @throws Exception
     */
    public function post(
        CommentRepository $commentRepository,
        PostRepository $postRepository,
        Post $post,
        Request $request): Response
    {
        
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        if($commentForm->isSubmitted() && $commentForm->isValid()) {
            if( null == $this->getUser()) {
                throw new Exception('You dont have any access here. You must logged first.');
            }
            /** @var User $user */
            $user=$this->getUser();
            $comment->setUser($user);
            $comment->setPost($post);
            $commentRepository->add($comment,true);
            return $this->redirectToRoute('app_post',['slug'=>$post->getSlug()]);
        }

        return $this->render('post/index.html.twig',[
            'posts' => $postRepository->findBy(['published'=>Post::PUBLISHED]),
            'post' => $post,
            'commentForm' => $commentForm->createView(),
            'comments'=> $commentRepository->findBy(['published'=>Comment::COMMENT_PUBLISHED, 'post' => $post->getId()])
        ]);
    }
}

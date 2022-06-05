<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Game;
use App\Entity\Message;
use App\Entity\Post;
use App\Entity\Review;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\MessageType;
use App\Form\ReviewType;
use App\Repository\CommentRepository;
use App\Repository\GameRepository;
use App\Repository\MessageRepository;
use App\Repository\PostRepository;
use App\Repository\ReviewRepository;
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
        $contactForm = $this->createForm(MessageType::class, new Message());
        $contactForm->handleRequest($request);

        if($contactForm->isSubmitted() && $contactForm->isValid())
        {
            /** @var User $user */
            $user = $this->getUser();
            /** @var Message $message */
            $message = $contactForm->getData();
            if( null != $user){
                $message->setEmail($user->getEmail());
            }

            $this->addFlash(
                'success',
                'message added succesfully'
            );
            $messageRepository->add($message,true);

            return new RedirectResponse($this->generateUrl('app_home'));
        }

        return $this->render('home/index.html.twig',[
            'posts' => $postRepository->findBy(['published'=>Post::PUBLISHED]),
            'topics' => $topicRepository->findAll(),
            'formContact' => $contactForm->createView()
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

    /**
     * @Route("/catalog/{page}", name="_catalog" , methods={"GET"})
     */
    public function catalog(Request $request,GameRepository $gameRepository, int $page = 1) : Response
    {
        $filter = $request->query->get('filter') ?? null;
        $response = $gameRepository->paginate($page,$filter);

        return $this->render('catalog/index.html.twig',[
            'games' => $response['games'],
            'recordsByPage' => $response['recordsByPage'],
            'total' => $response['total'],
        ]);
    }

    /**
     * @Route("/game/{slug}", name="_game" , methods={"GET","POST"})
     * @ParamConverter("game", class="App\Entity\Game")
     * @throws Exception
     */
    public function game(Game $game, Request $request, ReviewRepository $reviewRepository) : Response
    {
        $reviewForm = $this->createForm(ReviewType::class,new Review());
        $reviewForm->handleRequest($request);
        /** @var User $user */
        $user = $this->getUser();
        if($reviewForm->isSubmitted() && $reviewForm->isValid())
        {
            /** @var Review $review */
            $review = $reviewForm->getData();
            $review->setTitle((new \DateTimeImmutable())->format('Y-m-s H:i:s'));
            $game->setRating($this->calculateRating($game,$review->getRating()));
            $review->setGame($game);
            $review->setUser($user);

            $reviewRepository->add($review,true);
            $this->addFlash(
                'success',
                'review added succesfully'
            );
            return $this->redirectToRoute('app_game',['slug'=>$game->getSlug()]);
        }

        return $this->render('game/index.html.twig',[
            'game' => $game,
            'reviewForm' => $reviewForm->createView(),
            'reviews' => $reviewRepository->findAll()
        ]);
    }

    private function calculateRating(Game $game, $ratingOld) : int
    {
        $ratingUpdated = Review::MIN_VALUE_RATING;
        if(!$game->getReviews()->isEmpty()){

            $ratings = array_map(function($review){
                /** @var Review $review */
                return $review->getRating();
            }, $game->getReviews()->toArray());

            $ratings[] = $ratingOld;

            return intval(array_sum($ratings) / sizeof($ratings));

        }

        return $ratingOld;

    }
}

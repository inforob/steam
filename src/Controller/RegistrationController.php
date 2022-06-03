<?php

namespace App\Controller;

use App\Entity\User;
use App\Events\UserEvent;
use App\Form\UserRegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/register", name="register")
 */
class RegistrationController extends AbstractController
{
    private EventDispatcherInterface $dispatcher;

    private EntityManagerInterface $entityManager;

    public function __construct(EventDispatcherInterface $dispatcher,EntityManagerInterface $entityManager)
    {
        $this->dispatcher=$dispatcher;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/user/new", name="_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->dispatcher->dispatch(new UserEvent($user),UserEvent::REGISTER_ACTION);
                $this->addFlash(
                    'success',
                    'Registered user ' . $user->getUserIdentifier()
                );

                return $this->redirectToRoute('register_user_new', [], Response::HTTP_SEE_OTHER);
            } catch (Exception $exception) {
                $this->addFlash(
                    'fail',
                    $exception->getMessage()
                );
            }

        }

        return $this->renderForm('/user/registration/index.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/token/{token}", name="_user_activate", methods={"GET"})
     * @ParamConverter("user", class="App\Entity\User")
     * @throws Exception
     */
    public function activate(User $user, UserRepository $userRepository) :RedirectResponse
    {
        $user->setActivate(User::ACTIVATED);
        $user->setToken(null);

        $userRepository->add($user,true);

        $this->addFlash(
            'success',
            'Actived ' . $user->getUserIdentifier()
        );

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}

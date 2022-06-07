<?php

namespace App\Controller;

use App\Entity\User;
use App\Events\UserEvent;
use App\Form\UserResetPasswordType;
use App\Form\UserResetType;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetController extends AbstractController
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher=$dispatcher;
    }

    /**
     * @Route("/forgot", name="app_user_forgot", methods={"GET", "POST"})
     * @throws Exception
     */
    public function forgot(Request $request, UserRepository $userRepository) :Response
    {

        $form = $this->createForm(UserResetType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->getData()['emailReset'];

            /** @var User $user */
            $user = $userRepository->findOneBy(['email'=>$email]);
            if(null == $user){
                $this->addFlash(
                    'fail',
                    'this user not exist '
                );

                return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            }

            $user->setToken(md5(uniqid(rand(), true)));

            $this->dispatcher->dispatch(new UserEvent($user),UserEvent::RESET_PASSWORD);
            $userRepository->add($user,true);
            $this->addFlash(
                'success',
                'reset password process - ' . $user->getUserIdentifier() . ' - a email has been send for reset your password'
            );

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('user/reset/index.html.twig', [
            'form' => $form,
        ]);

    }

    /**
     * @Route("/reset/{token}", name="app_user_reset", methods={"GET", "POST"})
     * @throws Exception
     */
    public function reset(Request $request, string $token, UserRepository $userRepository) : Response
    {
        $user = $userRepository->findOneBy(['token'=>$token]);
        if(null == $user){
            throw new Exception("this token has been expired");
        }

        $form = $this->createForm(UserResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userRepository->add($user,true);
            $this->addFlash(
                'success',
                'Reset password user with success - ' . $user->getUserIdentifier() . ' - your password has been reset for your next login'
            );

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/reset/index.html.twig', [
            'form' => $form,
        ]);

    }
}

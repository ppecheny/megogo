<?php

namespace Ppecheny\UserBundle\Controller;

use Ppecheny\UserBundle\Entity\User;
use Ppecheny\UserBundle\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RegistrationController
 */
class RegistrationController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function registerAction(Request $request)
    {
        /** @var UserService $userService */
        $userService = $this->get('ppecheny.user.service');

        if ($userService->isAuthenticated()) {
            return $this->forward('PpechenyQuestionaryBundle:Question:current');
        }

        $form = $this->createForm('registrationType');

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $userService->createUser($user);

            $userService->setUserId($user->getId());

            return $this->forward('PpechenyQuestionaryBundle:Question:current');
        }

        return $this->render('PpechenyUserBundle:Registration:register.html.twig', array(
            'form' => $form->createView()
        ));
    }
}

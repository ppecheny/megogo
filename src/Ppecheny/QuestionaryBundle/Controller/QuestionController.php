<?php

namespace Ppecheny\QuestionaryBundle\Controller;

use Ppecheny\QuestionaryBundle\Services\QuestionService;
use Ppecheny\UserBundle\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class QuestionController
 */
class QuestionController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        /** @var UserService $userService */
        $userService = $this->get('ppecheny.user.service');

        if (!$userService->isTimeValid()) {
            $userService->clearSessionData();
        }

        return $this->render('PpechenyQuestionaryBundle:Question:index.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function currentAction(Request $request)
    {
        /** @var QuestionService $questionService */
        $questionService = $this->get('ppecheny.question.service');

        /** @var UserService $userService */
        $userService = $this->get('ppecheny.user.service');

        $form = $questionService->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $questionService->save($form, $userService->getUserId());

            return $this->forward('PpechenyQuestionaryBundle:Question:congratulation');
        }

        return $this->render('PpechenyQuestionaryBundle:Question:current.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @return Response
     */
    public function countdownAction()
    {
        /** @var UserService $userService */
        $userService = $this->get('ppecheny.user.service');

        $timeLeft = $userService->getTimeLeft();

        return $this->render('PpechenyQuestionaryBundle:Question:countdown.html.twig', array(
            'timeLeft' => $timeLeft
        ));
    }

    /**
     * @return Response
     */
    public function congratulationAction()
    {
        /** @var UserService $userService */
        $userService = $this->get('ppecheny.user.service');

        $userService->clearSessionData();

        return $this->render('PpechenyQuestionaryBundle:Question:congratulation.html.twig');
    }
}

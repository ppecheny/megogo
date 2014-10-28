<?php

namespace Ppecheny\QuestionaryBundle\Services;

use Doctrine\ORM\EntityManager;
use Ppecheny\QuestionaryBundle\Entity\Question;
use Ppecheny\QuestionaryBundle\Entity\UserAnswer;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class QuestionService
 */
class QuestionService
{
    const DATA_TYPE_TEXT = 'text';
    const DATA_TYPE_DATE = 'date';

    /**
     * @var Session $session
     */
    private $session;

    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var FormFactoryInterface $formFactory
     */
    private $formFactory;

    /**
     * @param Session              $session
     * @param EntityManager        $em
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(Session $session, EntityManager $em, FormFactoryInterface $formFactory)
    {
        $this->session = $session;
        $this->em = $em;
        $this->formFactory = $formFactory;
    }

    /**
     * @return Question[]
     */
    public function getQuestions()
    {
        return $this->em->getRepository('PpechenyQuestionaryBundle:Question')
            ->findAll();
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        $questions = $this->getQuestions();

        $form = $this->formFactory->createBuilder();

        foreach ($questions as $question) {
            $this->addField($form, $question);
        }

        $form
            ->add('save', 'submit');

        return $form->getForm();
    }

    /**
     * @param FormBuilderInterface $form
     * @param Question             $question
     */
    public function addField(FormBuilderInterface $form, Question $question)
    {
        $label = array(
            'label' => $question->getTitle()
        );

        switch ($question->getDataType()) {
            case self::DATA_TYPE_TEXT:
                $form
                    ->add($question->getId(), 'text', $label);
                break;
            case self::DATA_TYPE_DATE:
                $form
                    ->add($question->getId(), 'date', $label);
                break;
        }
    }

    /**
     * @param Form $form
     * @param int  $userId
     */
    public function save(Form $form, $userId)
    {
        $data = $form->getData();

        foreach ($data as $questionId => $answer) {
            $userAnswer = new UserAnswer();
            $userAnswer->setUserId($userId);
            $userAnswer->setQuestionId($questionId);

            $userAnswer->setAnswer($this->convertAnswer($answer));

            $this->em->persist($userAnswer);
        }

        $this->em->flush();
    }

    /**
     * @param mixed $answer
     *
     * @return string
     */
    public function convertAnswer($answer)
    {
        if ($answer instanceof \DateTime) {
            $answer = $answer->format('U');
        }

        return (string) $answer;
    }
}

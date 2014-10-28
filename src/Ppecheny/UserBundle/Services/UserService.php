<?php

namespace Ppecheny\UserBundle\Services;

use Doctrine\ORM\EntityManager;
use Ppecheny\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class UserService
 */
class UserService
{
    /**
     * @var Session $session
     */
    private $session;

    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var int $timeLimit
     */
    private $timeLimit;

    /**
     * @param Session       $session
     * @param EntityManager $em
     * @param int           $timeLimit
     */
    public function __construct(Session $session, EntityManager $em, $timeLimit)
    {
        $this->session = $session;
        $this->em = $em;
        $this->timeLimit = $timeLimit;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->session->get('user_id');
    }

    /**
     * @param int $userId
     *
     * @return int
     */
    public function setUserId($userId)
    {
        $this->session->set('user_id', $userId);
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        $userId = $this->getUserId();

        return $userId ? true : false;
    }


    /**
     * @param User $user
     */
    public function createUser(User $user)
    {
        $password = md5(time());

        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();
        $this->em->refresh($user);

        // @Todo send user email with password
    }

    /**
     * @return int
     */
    public function getCurrentTime()
    {
        $sessionTime = $this->session->get('time_start');

        if (!$sessionTime) {

            $sessionTime = time();

            $this->initTime($sessionTime);
        }

        return $sessionTime;
    }

    /**
     * @return int
     */
    public function getTimeLeft()
    {
        $sessionTime = $this->getCurrentTime();

        return $this->timeLimit - (time() - $sessionTime);
    }

    /**
     * @param int $time
     */
    public function initTime($time)
    {
        $this->session->set('time_start', $time);
    }

    /**
     * isTimeValid
     *
     * @return bool
     */
    public function isTimeValid()
    {
        return time() - $this->getCurrentTime() < $this->timeLimit ? true : false;
    }

    /**
     * clearSessionData
     */
    public function clearSessionData()
    {
        $this->session->remove('user_id');
        $this->session->remove('time_start');
    }
}

<?php

namespace Crearock\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Crearock\ProjectBundle\Entity\TempSupport
 */
class TempSupport
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var datetime $created_at
     */
    private $created_at;

    /**
     * @var Crearock\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var Crearock\ProjectBundle\Entity\Reward
     */
    private $reward;

    /**
     * @var Crearock\PaymentBundle\Entity\Transaction
     */
    private $transaction;

    
    public function __construct(){
        $this->created_at = new \DateTime;
        $this->transaction = null;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set user
     *
     * @param Crearock\UserBundle\Entity\User $user
     */
    public function setUser(\Crearock\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Crearock\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set reward
     *
     * @param Crearock\ProjectBundle\Entity\Reward $reward
     */
    public function setReward(\Crearock\ProjectBundle\Entity\Reward $reward)
    {
        $this->reward = $reward;
    }

    /**
     * Get reward
     *
     * @return Crearock\ProjectBundle\Entity\Reward 
     */
    public function getReward()
    {
        return $this->reward;
    }

    /**
     * Set transaction
     *
     * @param Crearock\PaymentBundle\Entity\Transaction $transaction
     */
    public function setTransaction(\Crearock\PaymentBundle\Entity\Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get transaction
     *
     * @return Crearock\PaymentBundle\Entity\Transaction 
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
<?php

namespace Crearock\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Crearock\ProjectBundle\Entity\Follow
 */
class Follow
{    
    /**
     * @var Crearock\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var Crearock\ProjectBundle\Entity\Project
     */
    private $project;
    
    /**
     * @var datetime $created_at
     */
    private $created_at;

    public function __construct(){
        $this->created_at = new \DateTime;
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
     * Set project
     *
     * @param Crearock\ProjectBundle\Entity\Project $project
     */
    public function setProject(\Crearock\ProjectBundle\Entity\Project $project)
    {
        $this->project = $project;
    }

    /**
     * Get project
     *
     * @return Crearock\ProjectBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
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
}
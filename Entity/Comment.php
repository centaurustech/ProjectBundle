<?php

namespace Crearock\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Crearock\ProjectBundle\Entity\Comment
 */
class Comment
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $comment
     */
    private $comment;

    /**
     * @var datetime $created_at
     */
    private $created_at;

    /**
     * @var Crearock\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var Crearock\ProjectBundle\Entity\Project
     */
    private $project;

    public function __construct(){
        $this->created_at = new \DateTime();
    }
        
    /**
     * Set id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Set comment
     *
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
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
}
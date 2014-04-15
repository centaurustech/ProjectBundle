<?php

namespace Crearock\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Crearock\ProjectBundle\Entity\Project;

/**
 * Crearock\ProjectBundle\Entity\Reward
 */
class Reward
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var integer $amount
     */
    private $amount;

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var string $description
     */
    private $description;
    
    /**
     * @var Crearock\ProjectBundle\Entity\Project
     */
    private $project;
    
    /**
     * @var integer $max_units
     */
    private $max_units;

    /**
     * @var integer $units
     */
    private $units;
    
    public function __contruct(){
        $this->units = 0;
        $this->max_units = 0;
    }
    
    public function __toString(){
        return $this->title;
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
     * Set amount
     *
     * @param integer $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set project
     *
     * @param Crearock\ProjectBundle\Entity\Project $project
     */
    public function setProject(Project $project)
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
     * Set max_units
     *
     * @param integer $maxUnits
     */
    public function setMaxUnits($maxUnits)
    {
        $this->max_units = $maxUnits;
    }

    /**
     * Get max_units
     *
     * @return integer 
     */
    public function getMaxUnits()
    {
        return $this->max_units;
    }

    /**
     * Set units
     *
     * @param integer $units
     */
    public function setUnits($units)
    {
        $this->units = $units;
    }

    /**
     * Get units
     *
     * @return integer 
     */
    public function getUnits()
    {
        return $this->units;
    }
    
    public function addUnit(){
        $this->units += 1; 
    }
    
    public function isFinished(){
        return ($this->max_units > 0 && $this->units >= $this->max_units);
    }    
}
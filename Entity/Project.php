<?php

namespace Crearock\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Crearock\ProjectBundle\Entity\Project
 */
class Project
{
    CONST UNDER_REVIEW = 0;
    CONST BEING_APPLAUDED = 1;
    CONST BEING_SUPPORTED = 2;
    CONST DECIDING = 3;
    CONST EXTENDED = 4;
    CONST ENDED_SUCCESSFULLY = 5;
    CONST ENDED_UNSUCCESSFULLY = 6;
    CONST MIN_APPLAUSE = 50;
    CONST EXTENDED_DAYS = 30;
    
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var string $image
     */
    private $image;

    /**
     * @var string $resume
     */
    private $resume;

    /**
     * @var text $description
     */
    private $description;

    /**
     * @var string $vurl
     */
    private $vurl;

    /**
     * @var string $aurl
     */
    private $aurl;

    /**
     * @var integer $amount
     */
    private $amount;

    /**
     * @var datetime $created_at
     */
    private $created_at;

    /**
     * @var datetime $extended_at
     */
    private $extended_at;

    /**
     * @var datetime $start_fund_at
     */
    private $start_fund_at;

    /**
     * @var integer $days
     */
    private $days;

    /**
     * @var string $url
     */
    private $url;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var Crearock\ProjectBundle\Entity\Reward
     */
    private $rewards;

    /**
     * @var Crearock\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var Crearock\ProjectBundle\Entity\ProjectCategory
     */
    private $category;
    
    /**
     * @var integer $applause
     */
    private $applause;

    public function __construct()
    {
        $this->created_at = new \DateTime;
        $this->applause = 0;
        $this->status = Project::UNDER_REVIEW;
        $this->rewards = new ArrayCollection();
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
     * Set image
     *
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set resume
     *
     * @param string $resume
     */
    public function setResume($resume)
    {
        $this->resume = $resume;
    }

    /**
     * Get resume
     *
     * @return string 
     */
    public function getResume()
    {
        return $this->resume;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set vurl
     *
     * @param string $vurl
     */
    public function setVurl($vurl)
    {
        $this->vurl = $vurl;
    }

    /**
     * Get vurl
     *
     * @return string 
     */
    public function getVurl()
    {
        return $this->vurl;
    }

    /**
     * Set aurl
     *
     * @param string $aurl
     */
    public function setAurl($aurl)
    {
        $this->aurl = $aurl;
    }

    /**
     * Get aurl
     *
     * @return string 
     */
    public function getAurl()
    {
        return $this->aurl;
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
     * Set extended_at
     *
     * @param datetime $extendedAt
     */
    public function setExtendedAt($extendedAt)
    {
        $this->extended_at = $extendedAt;
    }

    /**
     * Get extended_at
     *
     * @return datetime 
     */
    public function getExtendedAt()
    {
        return $this->extended_at;
    }

    /**
     * Set start_fund_at
     *
     * @param datetime $startFundAt
     */
    public function setStartFundAt($startFundAt)
    {
        $this->start_fund_at = $startFundAt;
    }

    /**
     * Get start_fund_at
     *
     * @return datetime 
     */
    public function getStartFundAt()
    {
        return $this->start_fund_at;
    }

    /**
     * Set days
     *
     * @param integer $days
     */
    public function setDays($days)
    {
        $this->days = $days;
    }

    /**
     * Get days
     *
     * @return integer 
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set status
     *
     * @param smallint $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return smallint 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add rewards
     *
     * @param Crearock\ProjectBundle\Entity\Reward $rewards
     */
    public function addReward(\Crearock\ProjectBundle\Entity\Reward $rewards)
    {
        $this->rewards[] = $rewards;
    }

    /**
     * Get rewards
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getRewards()
    {
        return $this->rewards;
    }

    /**
     * Set rewards
     *
     * @param Crearock\ProjectBundle\Entity\Reward $rewards
     */
    public function setRewards($rewards)
    {
        foreach ($rewards as $reward) {
            $reward->setProject($this);
        }

        $this->rewards = $rewards;
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
     * Set category
     *
     * @param Crearock\ProjectBundle\Entity\ProjectCategory $category
     */
    public function setCategory(\Crearock\ProjectBundle\Entity\ProjectCategory $category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return Crearock\ProjectBundle\Entity\ProjectCategory 
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * Set applause
     *
     * @param integer $applause
     */
    public function setApplause($applause)
    {
        $this->applause = $applause;
    }

    /**
     * Get applause
     *
     * @return integer 
     */
    public function getApplause()
    {
        return $this->applause;
    }
}
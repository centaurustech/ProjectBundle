<?php
//Crearock\ProjectBundle\DataFixtures\ORM\LoadReward.php

namespace Crearock\ProjectBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Crearock\ProjectBundle\Entity\Reward;

class LoadReward extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Main method for fixtures insertion
     * 
     * @param Doctrine\Manager $manager 
     */
    public function load(ObjectManager $manager)
    {
        $reward = new Reward();
        $reward->setAmount(10);
        $reward->setTitle('Envio disco');
        $reward->setDescription('Lorem ipsum dolor sit amet, consectetuer adipiscing eli');
        $reward->setProject();
        $manager->persist($reward);
        unset($reward);
        
        $reward->setAmount(20);
        $reward->setTitle('Envio disco + camiseta');
        $reward->setDescription('Lorem ipsum dolor sit amet, consectetuer adipiscing eli');
        $reward->setProject();
        $manager->persist($reward);
        unset($reward);
        
        $reward->setAmount(10);
        $reward->setTitle('Envio disco + camiseta + poster');
        $reward->setDescription('Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis,');
        $reward->setProject();
        $manager->persist($reward);
        unset($reward);               
        
        $manager->flush();
        
        //Associate a reference for other fixtures
//        $this->addReference('user-admin', $user);
    }
    
    /**
     * Get the order of this execution
     * 
     * @return int 
     */
    public function getOrder()
    {
        return 3;
    }
}
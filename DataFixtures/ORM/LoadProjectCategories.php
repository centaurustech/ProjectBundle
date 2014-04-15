<?php
//Crearock\ProjectBundle\DataFixtures\ORM\LoadProjectCategories.php

namespace Crearock\ProjectBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Crearock\ProjectBundle\Entity\ProjectCategory;

class LoadProjectCategories extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Main method for fixtures insertion
     * 
     * @param Doctrine\Manager $manager 
     */
    public function load(ObjectManager $manager)
    {
        $category = new ProjectCategory();
        $category->setName('Disco');
        $category->setEnabled(true);
        $category->setUrl('disco');
        $manager->persist($category);
        $this->addReference('category_disco', $category);
        unset($category);

        $category = new ProjectCategory();
        $category->setName('Videoclip');
        $category->setEnabled(true);
        $category->setUrl('videoclip');
        $manager->persist($category);
        $this->addReference('category_videoclip', $category);
        unset($category);
        
        $category = new ProjectCategory();
        $category->setName('Gira/Conciertos');
        $category->setEnabled(true);
        $category->setUrl('gira-conciertos');
        $manager->persist($category);
        $this->addReference('category_gira', $category);
        unset($category);
        
        $category = new ProjectCategory();
        $category->setName('Merchandising');
        $category->setEnabled(true);
        $category->setUrl('merchandising');
        $this->addReference('category_merchandising', $category);
        $manager->persist($category);
        unset($category);
        
        $category = new ProjectCategory();
        $category->setName('Diseños');
        $category->setEnabled(true);
        $category->setUrl('disenos');
        $manager->persist($category);
        $this->addReference('category_disenos', $category);
        unset($category);
        
        $category = new ProjectCategory();
        $category->setName('Otros');
        $category->setEnabled(true);
        $category->setUrl('otros');
        $manager->persist($category);
        $this->addReference('category_otros', $category);
        unset($category);

        $manager->flush();
    }
    
    /**
     * Get the order of this execution
     * 
     * @return int 
     */
    public function getOrder()
    {
        return 1;
    }
}
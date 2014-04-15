<?php
//Crearock\ProjectBundle\DataFixtures\ORM\LoadProject.php

namespace Crearock\ProjectBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Crearock\ProjectBundle\Entity\Project;

class LoadProject extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Main method for fixtures insertion
     * 
     * @param Doctrine\Manager $manager 
     */
    public function load(ObjectManager $manager)
    {
        $project = new Project();
        $project->setUser($this->getReference('user_david'));
        $project->setCategory($this->getReference('category_videoclip'));
        $project->setTitle('Lorem ipsum dolor sit amet, consectetuer adipiscing');
        $project->setImage('5f917d9ba479.jpeg');
        $project->setResume('Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et');
        $project->setDescription('Sed interdum velit id mi congue quis pharetra magna fermentum. Vestibulum pretium odio sit amet purus viverra accumsan. Morbi dolor mauris, volutpat vel dignissim ac, lobortis nec eros. Nulla mattis ornare quam ac imperdiet. Morbi suscipit facilisis risus quis vulputate. Curabitur consequat pharetra ullamcorper. Aenean sed neque nulla, nec rutrum nisi.Phasellus eget arcu velit, eu sodales nibh. Proin dapibus diam libero, sed ornare diam. Pellentesque sed tortor a risus tempus ullamcorper lobortis a nibh. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras nibh nisi, pharetra et placerat at, ullamcorper eu leo. Maecenas sed est ac turpis vehicula gravida. Morbi posuere tortor nunc, a sagittis felis. Suspendisse laoreet sem odio. Mauris id orci eu tellus malesuada malesuada. Aenean vitae neque sed dolor lacinia gravida sagittis eget enim. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut libero odio, dictum at sollicitudin ac, rutrum vitae ante. Quisque est ante, vehicula porta bibendum eu, laoreet a lectus. Nulla non lorem sem. Integer vehicula, nisl a imperdiet vestibulum, risus tellus lacinia nibh, sed pretium nulla massa at sem. Mauris volutpat sem eu ligula euismod non ultrices arcu cursus.');
        $project->setVurl('24120283');
        $project->setAurl('2F5858290');
        $project->setAmount(650);
        $project->setCreatedAt(new \DateTime('2012-04-17 09:43:08'));
        $project->setExtendedAt(new \DateTime('2012-04-17 09:43:08'));
        $project->setStartFundAt(new \DateTime('2012-04-18 09:14:08'));
        $project->setDays(10);
        $project->setStatus(2);
        $project->setUrl('ipsum-dolor-sit-amet-consectetuer-adipiscing');
        $manager->persist($project);
        $this->addReference('project1', $project);
        unset($project);
        
        $project = new Project();
        $project->setUser($this->getReference('user_dani'));
        $project->setCategory($this->getReference('category_disco'));
        $project->setTitle('Una mañana, tras un sueño');
        $project->setImage('660bc4820584.jpeg');
        $project->setResume('Una mañana, tras un sueño intranquilo');
        $project->setDescription('Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y oscuro, surcado por curvadas callosidades, sobre el que casi no se aguantaba la colcha, que estaba a punto de escurrirse hasta el suelo. Numerosas patas, penosamente delgadas en comparación con el grosor normal de sus piernas, se agitaban sin concierto. - ¿Qué me ha ocurrido? No estaba soñando. Su habitación, una habitación normal, aunque muy pequeña, tenía el aspecto habitual. Sobre la mesa había desparramado un muestrario de paños - Samsa era viajante de comercio-, y de la pared colgaba una estampa recientemente recortada de una revista ilustrada y puesta en un marco dorado. La estampa mostraba a una mujer tocada con un gorro de pieles, envuelta en una estola también de pieles, y que, muy erguida, esgrimía un amplio manguito, asimismo de piel, que ocultaba todo su antebrazo. Gregorio miró hacia la ventana; estaba nublado, y sobre el cinc del alféizar repiqueteaban las gotas de lluvia, lo que le hizo sentir una gran melancolía. «Bueno -pensó-; ¿y si siguiese durmiendo un rato y me olvidase de todas estas locuras? » Pero no era posible, pues Gregorio tenía la costumbre de dormir sobre el lado derecho, y su actual estado no le permitía adoptar tal postura. Por más que se esforzara volvía a quedar de espaldas. Intentó en vano esta operación numerosas veces; cerró los ojos para no tener que ver aquella confusa agitación de patas, que no cesó hasta que notó en el costado un dolor leve y punzante, un dolor jamás sentido hasta entonces. - ¡Qué cansada es la profesión que he elegido! -se dijo-. Siempre de viaje. Las preocupaciones son mucho mayores cuando se trabaja fuera, por no hablar de las molestias propias de los viajes: estar pendiente de los enlaces de los trenes; la comida mala, irregular; relaciones que cambian constantemente, que nunca llegan a ser verdaderamente cordiales, y en las que no tienen cabida los sentimientos. ¡Al diablo con todo! Sintió en el vientre una ligera picazón. Lentamente, se estiró sobre la espalda en dirección a la cabecera de la cama, para poder alzar mejor la cabeza. Vio que el sitio que le picaba estaba cubierto de extraños untitos blancos. Intentó rascarse con una pata; pero tuvo que retirarla inmediatamente, pues el roce le producía escalofríos. Una mañana, tras un sueño intranquilo, Gregorio Samsa se despertó convertido en un monstruoso insecto. Estaba echado de espaldas sobre un duro caparazón y, al alzar la cabeza, vio su vientre convexo y oscuro, surcado por curvadas callosidades, sobre el que casi no se aguantaba la colcha, que estaba a punto de escurrirse hasta el suelo. Numerosas patas, penosamente delgadas en comparación con el grosor normal de sus piernas, se agitaban sin concierto. - ¿Qué me ha ocurrido? No estaba soñando. Su habitación, una habitación normal, aunque muy pequeña, tenía el aspecto habitual. Sobre la mesa había.');
        $project->setVurl('24120283');
        $project->setAurl('2F5858290');
        $project->setAmount(200);
        $project->setCreatedAt(new \DateTime('2012-04-12 09:43:08'));
        $project->setExtendedAt(new \DateTime('2012-04-12 09:43:08'));
        $project->setStartFundAt(new \DateTime('2012-04-18 09:14:08'));
        $project->setDays(20);
        $project->setStatus(2);
        $project->setUrl('una-manana-tras-sueno-intranquilo');
        $this->addReference('project2', $project);
        $manager->persist($project);
        unset($project);
               
        
        $manager->flush();
    }
    
    /**
     * Get the order of this execution
     * 
     * @return int 
     */
    public function getOrder()
    {
        return 2;
    }
}
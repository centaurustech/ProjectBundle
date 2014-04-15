<?php
namespace Crearock\ProjectBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormError;

class addAdminFieldsSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // During form creation setData() is called with null as an argument
        // by the FormBuilder constructor. You're only concerned with when
        // setData is called with an actual Entity object in it (whether new
        // or fetched with Doctrine). This if statement lets you skip right
        // over the null condition.
        if (null === $data) {
            return;
        }

        // check if the product object is "new"
        if ($data->getId()) {
            $form->add($this->factory->createNamed('datetime', 'created_at', null, array('label' => 'Fecha de creación:',
                                            'widget' => 'single_text',
//                                            'date_format' => 'dd-MM-yyyy HH:mm:ss',
//                                            'required' => true
                                        )))
                ->add($this->factory->createNamed('datetime', 'start_fund_at', null, array('label' => 'Fecha de inicio de recaudación:',
                                                'widget' => 'single_text',
//                                                'date_format' => 'dd-MM-yyyy HH:mm:ss',
                                                'required' => false
                                            )))
                ->add($this->factory->createNamed('datetime', 'extended_at', null, array('label' => 'Fecha de inicio de prorroga:',
                                                'widget' => 'single_text',
//                                                'date_format' => 'dd-MM-yyyy HH:mm:ss',
                                                'required' => false
                                            )))
                ->add($this->factory->createNamed('integer', 'applause', null, array('label' => 'Aplausos:')))
                ->add($this->factory->createNamed('integer', 'status', null, array('label' => 'Estado:')));
        } else {
//            $builder = $this->factory->createBuilder(new \Crearock\ProjectBundle\Form\ProjectType());
            $builder = $this->factory->createNamedBuilder('checkbox', 'terms', null, array('property_path' => false));
//            $term_field = $builder->create('terms', 'checkbox', array('property_path' => 'false'))
            $term_field = $builder->addValidator(new CallbackValidator(function ($terms) {
                    if (!$terms->getData()) {
                        $terms->addError(new FormError('Debes aceptar los Términos de uso.'));
                    }

                }))->getForm();
            
            $form->add($term_field);
//            $name = $form->getName();
//           $form = $this->factory->createNamed(new \Crearock\ProjectBundle\Form\ProjectType(), $name);
/*            $builder = $this->factory->createBuilder(new \Crearock\ProjectBundle\Form\ProjectType());
            
            $builder->addValidator(new CallbackValidator(
                function (FormInterface $form){
                    $terms = $form->get('terms');
                    if (!$terms->getData()) {
                        $terms->addError(new FormError('Debes aceptar los Términos de uso.'));
                    }
                }
            ));
 */            
        }
    }
}
?>

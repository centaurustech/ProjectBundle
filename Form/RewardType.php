<?php
namespace Crearock\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RewardType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('amount', 'text')
            ->add('title', null, array( 'attr' => array('style' => 'font-weight:bold'),
                'attr' => array('class' => 'count'),))
            ->add('description', 'textarea', array('max_length' => 500,
                                    'attr' => array('class' => 'autosize count'),
                ))
            ->add('limit', 'checkbox', array( 'label' => 'Limitar recompensa a ',
                                            'attr' => array('style' => 'width: 15px; vertical-align: middle;'),
                                            'property_path' => false,
                                            'required' => false))
            ->add('max_units', 'text', array( 'label' => ' mecenas.',
                                            'max_length' => 4,
                                            'attr' => array('style' => 'font-size: 18px; vertical-align: bottom; text-align: center; width: 12%;',
                                                            'disabled' => 'disabled',
                                                            'readonly' => 'readonly')
                ));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Crearock\ProjectBundle\Entity\Reward',
            'property_path' => true,
            'trim' => true,
            'required' => true
        );
    }

    public function getName() {
        return 'reward';
    }
}
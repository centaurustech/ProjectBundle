<?php
namespace Crearock\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('comment', 'textarea', array('attr' => array('class' => 'autoresize')
                ));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Crearock\ProjectBundle\Entity\Comment',
            'property_path' => true,
            'trim' => true,
            'required' => true,
        );
    }

    public function getName() {
        return 'comment_form';
    }
}
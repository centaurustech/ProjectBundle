<?php

namespace Crearock\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
//use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\Form\FormError;
use Crearock\ProjectBundle\Form\EventListener\addAdminFieldsSubscriber;
use Crearock\ProjectBundle\Repository\ProjectRepository;
use Crearock\ProjectBundle\Entity\Reward;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('category', 'entity', array('label' => 'Tipo de Obra:',
                                                'empty_value' => '',
                                                'required' => true,
                                                'class' => 'ProjectBundle:ProjectCategory',
                                                'query_builder' => 
                                                    function (ProjectRepository $repository){
                                                            return $repository->createQueryBuilder('ProjectBundle:ProjectCategory')
//                                                                    ->select('c.id, c.name')
//                                                                    ->from('ProjectBundle:ProjectCategory', 'cat')
                                                                    ->where('ProjectBundle:ProjectCategory.enabled = :enabled')
                                                                    ->setParameter('enabled', true)
                                                                    ->orderBy('ProjectBundle:ProjectCategory.name', 'ASC');
                                                    }))
            ->add('title', null, array('label' => 'Nombre del proyecto:',
                                        ))
            ->add('image', 'hidden', array('label' => 'Imagen del proyecto:',
                                                'error_bubbling' => false
                                        ))
            ->add('resume', 'textarea', array('label' => 'Descripción resumida del proyecto:',
                                                 'max_length' => 150,
                                        ))
            ->add('description', null, array('label' => 'Descripción detallada del proyecto:',
                                        'attr' => array('class' => 'mceEditor'),
                                                'required' => false,))
            ->add('vurl', null, array('label' => 'Enlace vídeo:',
                                        ))
            ->add('aurl', null, array('label' => 'mp3 en Soundcloud (opcional):',
                                                'required' => false,
                                        ))
            ->add('amount', 'text', array('label' => 'Cantidad a recaudar:',
                                        ))
            ->add('days', 'text', array('label' => 'Días para recaudar fondos:'))
            ->add('rewards', 'collection', array('type'         => new RewardType(),
                                                'allow_add'     => true,
                                                'allow_delete'  => true,
                                                'by_reference'  => false,
                                                'prototype'     => true,
                                        ));
/*           ->add('terms', 'checkbox', array('property_path' => false,
                                        ));
*/         
        $listener = new addAdminFieldsSubscriber($builder->getFormFactory());
        $builder->addEventSubscriber($listener);
                                                    
/*        if ($builder->has('terms')) {
            $builder->addValidator(new CallbackValidator(
                function (FormInterface $form){
                    $terms = $form->get('terms');
                    if (!$terms->getData()) {
                        $terms->addError(new FormError('Debes aceptar los Términos de uso.'));
                    }
                }
            ));
        }*/
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Crearock\ProjectBundle\Entity\Project',
            'property_path' => true,
            'trim' => true,
            'required' => true,
        );
    }

    public function getName() {
        return 'new_project_form';
    }
}

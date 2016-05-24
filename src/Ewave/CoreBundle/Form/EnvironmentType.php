<?php

namespace Ewave\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EnvironmentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'description',
                'textarea',
                array(
                    'required' => false
                )
            )
            ->add('submit', 'submit', array('label' => 'Save'))
        ;
        $builder->add(
            'type',
            'choice',
            array(
                'required' => true,
                'choices' =>
                        array(
                            'Develop' => 'Develop',
                            'Staging' => 'Staging',
                            'UAT' => 'UAT',
                            'Production' => 'Production',
                        )
                ,
                'label' => 'Type'
            )
        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }
}

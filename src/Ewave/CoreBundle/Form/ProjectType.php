<?php

namespace Ewave\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    private $_teams = array();

    public function __construct($teams) {
        foreach ($teams as $team) {
            $this->_teams[$team['id']] = $team['title'];
        }
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('description', 'textarea')
            ->add('submit', 'submit', array('label' => 'Save'))
        ;
        $builder->add(
            'team',
            'choice',
            array(
                'required' => false,
                'choices' =>
                        array('' => 'Select a team') + $this->_teams
                ,
                'label' => 'Team'
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
        return 'ewave_corebundle_project';
    }
}

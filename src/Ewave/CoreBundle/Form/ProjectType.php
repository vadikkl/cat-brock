<?php

namespace Ewave\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    private $_teams = array();

    private $_users = array();

    public function __construct($teams, $users) {
        foreach ($teams as $team) {
            $this->_teams[$team['id']] = $team['title'];
        }
        foreach ($users as $user) {
            $this->_users[$user['id']] = $user['username'];
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
        if (count($this->_users)) {
            $builder->add(
                'users',
                'choice',
                array(
                    'choices' => $this->_users,
                    'label' => 'Users',
                    'multiple' => true,
                    'expanded' => true
                )
            );
        }
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

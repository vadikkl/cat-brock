<?php

namespace Ewave\CoreBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    static public $ROLES = array(
        'ROLE_MANAGER' => 'Manager',
        'ROLE_TL' => 'Team Lead',
        'ROLE_BA' => 'Business Analyst',
        'ROLE_DEV' => 'Developer',
        'ROLE_DEV_ROSTER' => 'Developer on Roster',
        'ROLE_SUP' => 'Support Specialist',
        'ROLE_EEG' => 'EEG Specialist',
        'ROLE_SA' => 'SA Specialist'
    );

    static public $HIDDEN_ROLES = array(
        'ROLE_ADMIN' => 'Administrator',
        'ROLE_USER' => ''
    );

    private $_teams = array();

    private $_projects = array();

    public function __construct($teams, $projects) {
        foreach ($teams as $team) {
            $this->_teams[$team['id']] = $team['title'];
        }
        foreach ($projects as $project) {
            $this->_projects[$project['id']] = $project['title'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'username',
            'text',
            array(
                'label' => 'Username',
                'attr' => array('class' => 'form-control')
            )
        );
        $builder->add(
            'roles',
            'choice',
            array(
                'choices' => array_merge(
                    self::$ROLES
                ) ,
                'label' => 'Roles',
                'multiple' => true,
                'expanded' => true
            )
        );
        $builder->add(
            'team',
            'choice',
            array(
                'choices' => $this->_teams,
                'label' => 'Team'
            )
        );
        $builder->add(
            'email',
            'email',
            array(
                'label' => 'Email',
                'attr' => array('class' => 'form-control')
            )
        );
        $builder->add(
            'password',
            'password',
            array(
                'label' => 'Password',
                'attr' => array('class' => 'form-control')
            )
        );
        $builder->add(
            'password_confirm',
            'password',
            array(
                'label' => 'Confirm password',
                'attr' => array('class' => 'form-control')
            )
        );
        $builder->add(
            'enabled',
            'checkbox',
            array(
                'label' => 'Enabled',
                'required' => false,
                'attr' => array('class' => 'form-control')
            )
        );
        if (count($this->_projects)) {
            $builder->add(
                'projects',
                'choice',
                array(
                    'choices' => $this->_projects,
                    'label' => 'Projects',
                    'multiple' => true,
                    'expanded' => true
                )
            );
        }

        $builder->add('submit', 'submit', array('label' => 'Save'));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return '';
    }
}

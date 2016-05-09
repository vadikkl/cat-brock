<?php

namespace Ewave\CoreBundle\Form;

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

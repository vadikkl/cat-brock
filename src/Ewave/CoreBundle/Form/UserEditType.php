<?php

namespace Ewave\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserEditType extends AbstractType
{

    private $_teams = array();

    public function __construct($teams) {
        foreach ($teams as $team) {
            $this->_teams[$team['id']] = $team['title'];
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
            'email',
            'email',
            array(
                'label' => 'Email',
                'attr' => array('class' => 'form-control')
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
            'roles',
            'choice',
            array(
                'choices' => array_merge(
                    UserType::$ROLES
                ) ,
                'label' => 'Roles',
                'multiple' => true,
                'expanded' => true
            )
        );
        $builder->add(
            'password',
            'password',
            array(
                'label' => 'Password',
                'required' => false,
                'attr' => array('class' => 'form-control')
            )
        );
        $builder->add(
            'password_confirm',
            'password',
            array(
                'label' => 'Confirm password',
                'required' => false,
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

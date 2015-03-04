<?php

namespace Vilka\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SettingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('value', 'text')
            ->add('description', 'textarea')
            ->add('submit', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Vilka\CoreBundle\Entity\Setting',
            'csrf_protection' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vilka_corebundle_setting';
    }
}

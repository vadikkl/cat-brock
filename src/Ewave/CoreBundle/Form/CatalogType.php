<?php

namespace Ewave\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Ewave\CoreBundle\Entity\Catalog;

class CatalogType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'platform',
            'choice',
            array(
                'choices' => array_merge(
                    array('' => 'Выберите источник'),
                    Catalog::$PLATFORMS
                ) ,
                'label' => 'Platform',
                'attr' => array('class' => 'form-control')
            )
        );

        $builder->add(
            'offers',
            'choice',
            array(
                'choices' => array(),
                'label' => 'Platform',
                'multiple'=>true,
                'expanded'=>true
            )
        );

        $builder->add('submit', 'submit', array('label' => 'Далее'));
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
        return 'ewave_corebundle_catalog';
    }
}

<?php
namespace Vilka\CoreBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
class SearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'search',
            'text',
            array(
                'label' => 'Поиск',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Искать...'
                )
            )
        );

    }
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
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
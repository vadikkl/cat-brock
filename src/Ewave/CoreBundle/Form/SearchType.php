<?php
namespace Ewave\CoreBundle\Form;
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
                'label' => 'Search',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Search Text...'
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
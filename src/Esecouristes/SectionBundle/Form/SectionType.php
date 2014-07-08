<?php

namespace Esecouristes\SectionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SectionType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('sectionParent')
            ->add('nomLong')
            ->add('adresse')
            ->add('codePostal')
            ->add('ville')
            ->add('cedex')
            ->add('telephone')
            ->add('portableUrgence')
            ->add('fax')
            ->add('email')
            ->add('emailSecretariat')
            ->add('siteWeb')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Esecouristes\SectionBundle\Entity\Section'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'esecouristes_sectionbundle_section';
    }
}

<?php

namespace IUTO\LivretBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresentationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('editoLivret', TextareaType::class, array(
                'label' => 'Présentation du département',
                'attr' => array(
                    'placeholder' => 'Veuillez écrire la présentation ici...',
                    'rows' => 15,
                    'cols' => 100
                )
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Valider',
                'attr' => array(
                    'class' => 'buttonform'
                )
            ))
            ->add('previsualiser', SubmitType::class, array(
                    'label' => 'Prévisualiser',
                    'attr' => array(
                        'class' => 'buttonform'
                    )
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IUTO\LivretBundle\Entity\Livret'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'iuto_livretbundle_projet';
    }


}

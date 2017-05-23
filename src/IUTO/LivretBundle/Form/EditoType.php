<?php

namespace IUTO\LivretBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, array(
                'label' => 'Titre de l\'édito',
            ))
            ->add('contenuEdito', TextareaType::class, array(
                'label' => 'Contenu',
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary',
                ]
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IUTO\LivretBundle\Entity\Edito',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'iuto_livretbundle_edito';
    }


}

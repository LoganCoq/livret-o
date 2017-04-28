<?php

namespace IUTO\LivretBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AddImageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'label' => 'Ajout d\'une image',
                'required' => true,
                'attr' => [
                    'class' => 'btn btn-default'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter l\'image',
                'attr' => [
                    'class' => 'btn btn-success',
                    'data-toggle' => 'confirmation',
                    'data-singleton' => true,
                    'data-popout' => true,
                    'data-title' => 'Êtes-vous sûr ?',
                    'data-content' => 'L\'image sera ajoutée au projet',
                    'data-btn-ok-label' => 'Continuer',
                    'data-btn-cancel-label' => 'Annuler'
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IUTO\LivretBundle\Entity\Image'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'iuto_livretbundle_image';
    }


}

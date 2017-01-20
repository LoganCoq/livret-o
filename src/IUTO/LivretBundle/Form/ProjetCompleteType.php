<?php

namespace IUTO\LivretBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetCompleteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('intituleProjet', TextType::class, array(
                'label' => 'Intitulé du projet',
            ))
            ->add('descripProjet', TextareaType::class, array(
                'label' => 'Description',
                'required' => false,
            ))
            ->add('bilanProjet', TextareaType::class, array(
                'label' => 'Bilan du projet',
                'required' => false,
            ))
            ->add('clientProjet', TextType::class, array(
                'label' => 'Client',
                'required' => false,
            ))
            ->add('motsClesProjet', CollectionType::class, array( //TODO
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_data' => 'New Tag Placeholder',
                'required' => false,
            ))
            ->add('dateDebut', DateType::class, array(
                'label' => 'Date de début',
            ))
            ->add('dateFin', DateType::class, array(
                'label' => 'Date de fin',
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Enregistrer',
            ))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IUTO\LivretBundle\Entity\Projet'
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

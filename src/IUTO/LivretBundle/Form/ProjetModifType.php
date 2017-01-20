<?php

namespace IUTO\LivretBundle\Form;

use IUTO\LivretBundle\Entity\Etudiant;
use IUTO\LivretBundle\Entity\Personnel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetModifType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('intituleProjet', TextType::class, array(
          'label' => 'Intitulé du projet'
        ))
        ->add('dateDebut', DateType::class, array(
          'label' => 'Date de début'
        ))
        ->add('dateFin', DateType::class, array(
          'label' => 'Date de fin'
        ))
        ->add('etudiants', TextType::class, array(
          'label' => 'Étudiants'
        ))
        ->add('personnels', TextType::class, array(
          'label' => 'Professeur(s)'
        ))        ;
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

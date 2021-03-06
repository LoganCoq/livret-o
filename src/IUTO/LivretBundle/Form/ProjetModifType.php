<?php

namespace IUTO\LivretBundle\Form;

use IUTO\LivretBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
        ->add('dateDebut', TextType::class, array(
          'label' => 'Date de début',
            'attr' => [
                'data-provide' => 'datepicker',
                'class' => 'datepicker',
            ],
        ))
        ->add('dateFin', TextType::class, array(
          'label' => 'Date de fin',
            'attr' => [
                'data-provide' => 'datepicker',
                'class' => 'datepicker',
            ],
        ))
        ->add('etudiants', EntityType::class, array(
            'class' => User::class,
            'label' => 'Etudiants',
            'choice_label' => function (User $etudiant) {
                return $etudiant->getNomUser() . ' ' . $etudiant->getPrenomUser();
            },
            'multiple' => true,
            'choices' => $options['etudiants'],
            'attr' => [
                'class' => 'selectpicker',
                'data-live-search' => true,
                'data-width' => 'auto',
                'id' => 'livreto_project_students',
            ],
        ))
        ->add('tuteurs', EntityType::class, array(
            'class' => User::class,
            'choice_label' => function (User $personnel) {
                return $personnel->getNomUser() . ' ' . $personnel->getPrenomUser();
            },
            'multiple' => true,
            'choices' => $options['tuteurs'],
            'attr' => [
                'class' => 'selectpicker',
                'data-live-search' => true,
                'data-width' => 'auto',
                'id' => 'livreto_project_tuteurs',
            ],
        ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Suivant',
                'attr' => [
                    'class' => 'btn btn-primary',
//                    'data-toggle' => 'confirmation',
//                    'data-singleton' => true,
//                    'data-popout' => true,
//                    'data-title' => 'Êtes-vous sûr ?',
//                    'data-content' => 'L\'image sera ajoutée au projet',
//                    'data-btn-ok-label' => 'Continuer',
//                    'data-btn-cancel-label' => 'Annuler'
                ],
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IUTO\LivretBundle\Entity\Projet',
            'etudiants' => array(),
            'tuteurs' => array(),
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

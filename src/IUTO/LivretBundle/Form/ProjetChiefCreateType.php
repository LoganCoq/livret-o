<?php

namespace IUTO\LivretBundle\Form;

use IUTO\LivretBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetChiefCreateType extends AbstractType
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
                    'class' => 'datepicker from_date',
                ],
            ))
            ->add('dateFin', TextType::class, array(
                'label' => 'Date de fin',
                'attr' => [
                    'data-provide' => 'datepicker',
                    'class' => 'datepicker to_date',
                ],
            ))
            ->add('nomDep', ChoiceType::class, array(
                'label' => 'Département',
                'choices' => array(
                    'Chimie' => 'Chimie',
                    'GEA' => 'GEA',
                    'GMP' => 'GMP',
                    'GTE' => 'GTE',
                    'Informatique' => 'Informatique',
                    'QLIO' => 'QLIO',
                 ),
                'multiple' => false,
                'attr' => [
                    'class' => 'selectpicker departement',
                    'data-live-search' => 'true',
                    'data-selected-text-format' => 'count > 5',
                    'data-count-selected-text' => 'Tous les départements',
                    'title' => 'Aucun département sélectionné'
                ],
            ))
            ->add('etudiants', EntityType::class, array(
                'class' => User::class,
                'label' => 'Etudiants',
                'choice_label' => function (User $etudiant) {
                    return $etudiant->getNomUser() . ' ' . $etudiant->getPrenomUser();
                },
                'choices' => $options['etudiants'],
                'multiple' => true,
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => true,
                    'data-width' => 'auto',
                    'id' => 'livreto_project_studs',
                ],
            ))
            ->add('tuteurs', EntityType::class, array(
                'class' => User::class,
                'label' => 'Tuteurs',
                'required' => false,
                'choice_label' => function (User $personnel) {
                    return $personnel->getNomUser() . ' ' . $personnel->getPrenomUser();
                },
                'multiple' => true,
                'choices' => $options['tuteurs'],
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => true,
                    'data-width' => 'auto',
                    'id' => 'livreto_project_tuts',
                ],
            ))
            ->add('clientProjet', TextType::class, array(
                'label' => 'Client',
            ))
            ->add('descriptionClientprojet', TextareaType::class, array(
                'label' => 'Description client',
            ))
            ->add('descripProjet', TextareaType::class, array(
                'label' => 'Description du projet',
            ))
            ->add('bilanProjet', TextareaType::class, array(
                'label' => 'Bilan du projet',
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Suivant',
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

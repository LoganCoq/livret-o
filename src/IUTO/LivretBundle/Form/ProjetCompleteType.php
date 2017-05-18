<?php

namespace IUTO\LivretBundle\Form;

use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\IUTOLivretBundle;
use IUTO\LivretBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            ->add('clientProjet', TextType::class, array(
                'label' => 'Client',
                'required' => false,
            ))
            ->add('descriptionClientProjet', TextareaType::class, array(
                'label' => 'Description du client',
                'required' => false,
            ))
            ->add('descripProjet', TextareaType::class, array(
                'label' => 'Description',
                'required' => false,
            ))
            ->add('bilanProjet', TextareaType::class, array(
                'label' => 'Bilan du projet',
                'required' => false,
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
                'class' => 'IUTOLivretBundle:User',
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
                    'id' => 'livreto_project_studs',
                ],
            ))
            ->add('tuteurs', EntityType::class, array(
                'class' => User::class,
                'label' => 'Tuteurs',
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
            ->add('submit', SubmitType::class, array(
                'label' => 'Enregistrer modifications et continuer',
                'attr' => [
                    'class' => 'btn-primary',
                ],
            ))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IUTO\LivretBundle\Entity\Projet',
            'allow_extra_fields' => true,
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

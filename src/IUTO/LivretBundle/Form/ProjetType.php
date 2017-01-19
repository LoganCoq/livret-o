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

class ProjetType extends AbstractType
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
            ->add('nomDep', TextType::class, array(
                'disabled' => true,
                'label' => 'Département'))
            ->add('etudiants', EntityType::class, array(
                'class' => Etudiant::class,
                'choice_label' => function($etudiant) {
                    return $etudiant->getNomEtu() . ' ' . $etudiant->getPrenomEtu();
                },
                'multiple' => true,
//                'choices' => $options['listeEtudiants'], TODO
            ))
            ->add('personnels', EntityType::class, array(
                'class' => Personnel::class,
                'choice_label' => function($personnel) {
                    return $personnel->getNomPers() . ' ' . $personnel->getPrenomPers();
                },
                'multiple' => true,
            ))
            ->add('submit', SubmitType::class);
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

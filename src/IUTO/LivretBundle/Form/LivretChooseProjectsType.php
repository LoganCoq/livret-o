<?php

namespace IUTO\LivretBundle\Form;

use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivretChooseProjectsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('projects', EntityType::class, array(
                'class' => Projet::class,
                'label' => 'Projets',
                'choice_label' => function (Projet $projet) {
                    return $projet->getIntituleProjet();
                },
                'multiple' => true,
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => true,
                    'data-width' => 'auto',
                ],
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Valider la selection',
                'attr' => [
                    'class' => 'btn btn-primary',
                    'data-toggle' => 'confirmation',
                    'data-singleton' => true,
                    'data-popout' => true,
                    'data-title' => 'Êtes-vous sûr ?',
                    'data-content' => 'Le livret sera créer avec les projets sélectionnés',
                    'data-btn-ok-label' => 'Continuer',
                    'data-btn-cancel-label' => 'Annuler'
                ]
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'iuto_livretbundle_livret_projects';
    }


}

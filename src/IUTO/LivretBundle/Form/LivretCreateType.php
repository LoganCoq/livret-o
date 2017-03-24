<?php

namespace IUTO\LivretBundle\Form;

use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class LivretCreateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('listeDepartements', ChoiceType::class, array(
                'label' => 'Sélectionner les départements',
                'attr' => [
                  'class' => 'selectpicker departement',
                  'data-live-search' => 'true',
                  'multiple' => 'multiple',
                  'name' => 'departement',
                  'data-selected-text-format' => 'count > 5',
                  'data-count-selected-text' => 'Tous les départements',
                  'title' => 'Aucun département sélectionné'
                  ],
                  'choices' => array(
                    'Informatique' => 'Informatique',
                    'GMP' => 'GMP',
                    'GTE' => 'GTE',
                    'Chimie' => 'Chimie',
                    'GEA' => 'GEA',
                    'QLIO' => 'QLIO'
                  )
            ))
            ->add('listeFormation', ChoiceType::class, array(
                'label' => 'Sélectionner les promotions',
                'attr' => [
                  'class' => 'selectpicker annee',
                  'data-live-search' => 'true',
                  'multiple' => 'multiple',
                  'name' => 'annee',
                  'data-selected-text-format' => 'count > 3',
                  'data-count-selected-text' => 'Toutes les promotions',
                  'title' => 'Aucune promotion sélectionnée'
                  ],
                  'choices' => array(
                    '1A' => '1A',
                    '2A' => '2A',
                    'LP' => 'LP',
                    'AS' => 'AS'
                  )
            ))
            ->add('dateDebut', DateType::class, array(
                'label' => 'Sélectionner les projets se trouvant après cette date'
            ))
            ->add('dateFin', DateType::class, array(
                'label' => 'Sélectionner les projets se trouvant avant cette date'
            ))
            ->add('projetMarquants', CheckboxType::class, array(
                'label' => 'Projets marquants seulements',
                'required' => false
              ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Générer PDF',
              ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'iuto_livretbundle_livret';
    }


}

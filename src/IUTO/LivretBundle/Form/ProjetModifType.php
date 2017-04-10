<?php

namespace IUTO\LivretBundle\Form;

use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        ->add('etudiants', EntityType::class, array(
            'class' => User::class,
            'choice_label' => function (User $etudiant) {
                return $etudiant->getNomUser() . ' ' . $etudiant->getPrenomUser();
            },
            'multiple' => true,
            'query_builder' => function (UserRepository $er) use ($options) {
                $options;
                return $er->createQueryBuilder('u')
                    ->select('u')
                    ->where("u.role = 'Etudiant'");
            }
        ))
        ->add('tuteurs', EntityType::class, array(
            'class' => User::class,
            'choice_label' => function (User $personnel) {
                return $personnel->getNomUser() . ' ' . $personnel->getPrenomUser();
            },
            'multiple' => true,
            'query_builder' => function (UserRepository $er) {
                return $er->createQueryBuilder('u')
                    ->select('u')
                    ->where("u.role <> 'Etudiant'");
            }
        ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Suivant',
                'attr' => [
                    'onclick' => "return confirm('Etes vous sûr ?')",
                ],
            ));
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

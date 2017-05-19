<?php

namespace IUTO\LivretBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserModifType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idUniv', TextType::class, array(
                'label' => "Numéro universitaire",
            ))
            ->add('nomUser', TextType::class, array(
                'label' => "Nom",
            ))
            ->add('prenomUser', TextType::class, array(
                'label' => "Prénom",
            ))
            ->add('mailUser', TextType::class, array(
                'label' => 'Adresse mail',
            ))
            ->add('roles', ChoiceType::class, array(
                'label' => 'Roles',
                'choices' => array(
                    'ROLE_chief' => "ROLE_chief",
                    'ROLE_student' => 'ROLE_student',
                    'ROLE_faculty' => 'ROLE_faculty',
                    'ROLE_admin' => 'ROLE_admin',
                    'ROLE_employee' => 'ROLE_employee',
                    'ROLE_affiliate' => 'ROLE_affiliate',
                ),
                'multiple' => true,
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Valider les modifications',
                'attr' => [
                    'class' => 'btn btn-warning',
                ]
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IUTO\LivretBundle\Entity\User',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'iuto_livretbundle_user_modif';
    }


}

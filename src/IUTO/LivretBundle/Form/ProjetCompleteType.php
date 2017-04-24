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
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => true,
                    'data-width' => 'auto',
                    'id' => 'livreto_project_studs',
                ],
                'query_builder' => function (UserRepository $er) use ($options) {
                    $options;
                    return $er->createQueryBuilder('u')
                        ->select('u')
                        ->where("u.role = 'Etudiant' or u.role = 'ROLE_student'");
                }
            ))
            ->add('tuteurs', EntityType::class, array(
                'class' => User::class,
                'label' => 'Tuteurs',
                'choice_label' => function (User $personnel) {
                    return $personnel->getNomUser() . ' ' . $personnel->getPrenomUser();
                },
                'multiple' => true,
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => true,
                    'data-width' => 'auto',
                    'id' => 'livreto_project_tuts',
                ],
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->select('u')
                        ->where("u.role <> 'Etudiant' and u.role <> 'ROLE_student' and u.role <> 'ROLE_employee'");
                }
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Enregistrer modifications et continuer',
                'attr' => [
                    'onclick' => "return confirm('Etes vous sûr ?')",
                    'class' => 'btn-primary',
                ],
//                'attr' => [
//                    'data-toggle' => "confirmation",
//                    'class' => "btn-default confirmation",
//                    'style' => "margin: 2em 10em;",
//                ],
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

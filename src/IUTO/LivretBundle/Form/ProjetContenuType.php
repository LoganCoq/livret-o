<?php

namespace IUTO\LivretBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetContenuType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('clientProjet', TextType::class, array(
                'label' => 'Client',
                'required' => false,
            ))
            ->add('descriptionClientProjet', TextareaType::class, array(
                'label' => 'Description du client',
                'required' => false,
            ))
            ->add('descripProjet', TextareaType::class, array(
                'label' => 'Description du projet',
                'required' => false,
            ))
            ->add('bilanProjet', TextareaType::class, array(
                'label' => 'Bilan du projet',
                'required' => false,
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Enregistrer le contenu et continuer',
                'attr' => [
                    'class' => "btn-primary",
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

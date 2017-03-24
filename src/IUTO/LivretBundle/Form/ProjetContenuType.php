<?php

namespace IUTO\LivretBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        ->add('descripProjet', TextType::class, array(
            'label' => 'Description du projet',
            'required' => false,
        ))
        ->add('bilanProjet', TextType::class, array(
            'label' => 'Bilan du projet',
            'required' => false,

        ));
//        ->add('submit', SubmitType::class, array(
//            'label' => 'Enregistrer le contenu et envoyer en correction',
//            'attr' => [
//                'onclick' => "return confirm('Etes vous sûr ?')",
//            ],
//        ));
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

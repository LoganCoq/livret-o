<?php

namespace IUTO\LivretBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireCreateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('contenu', TextareaType::class, array(
            'label' => 'Message',
            'attr' => [
                'required' => true,
            ]
        ))
        ->add('submit', SubmitType::class, array(
            'label' => 'Envoyer',
            'attr' => [
                'onclick' => 'ftc()',
            ]
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
        return 'iuto_livretbundle_commentaire';
    }


}

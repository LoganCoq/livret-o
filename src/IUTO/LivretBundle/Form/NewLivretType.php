<?php

namespace IUTO\LivretBundle\Form;

use IUTO\LivretBundle\Entity\Edito;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewLivretType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('intituleLivret', TextType::class, array(
                'label' => 'Intitulé livret',
                'required' => true,
            ))
            ->add('editoLivret', EntityType::class, array(
                'label' => 'Edito livret',
                'class' => Edito::class,
                'choice_label' => function (Edito $edito) {
                    return $edito->getTitre();
                },
                'multiple' => true,
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => true,
                    'data-width' => 'auto',
                    'id' => 'livreto_editos',
                ]
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Passer au choix des projets',
                'attr' => [
                    'data-toggle' => 'confirmation',
                    'data-singleton' => true,
                    'data-popout' => true,
                    'data-title' => 'Êtes-vous sûr ?',
                    'data-content' => 'Le livret sera créer',
                    'data-btn-ok-label' => 'Continuer',
                    'data-btn-cancel-label' => 'Annuler'
                ]
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IUTO\LivretBundle\Entity\Livret',
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

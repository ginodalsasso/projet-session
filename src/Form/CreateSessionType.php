<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Formation;
use App\Entity\Stagiaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class CreateSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomSession', TextType::class)
            ->add('nbPlaces', IntegerType::class)
            ->add('dateDebut', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateFin', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            // ->add('stagiaires', EntityType::class, [
            //     'class' => Stagiaire::class,
            //     'choice_label' => 'id'
            // ])
            ->add('formation', EntityType::class, [
                'class' => Formation::class,
                'choice_label' => 'nomFormation',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}

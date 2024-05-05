<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Sequentially;

class CreateCategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomCategorie', TextType::class, [
                'required' => true,
                //nous pourrions ajouter directement les contraintes dans l'entité voulue (préférable) (voir l'entité cat&gorie)
                //new sequentially permet de ne pas spammer l'utilisateur de message de contrainte et s'arrêter à la première contrainte non respectée
                'constraints' => new Sequentially([
                    new Length(min: 5),
                    new Regex('^[a-z0-9_-]{3,15}$', message: "Ceci n'est pas une catégorie valide")
                ])
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}

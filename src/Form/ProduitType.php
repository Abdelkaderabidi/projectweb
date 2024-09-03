<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;


class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomProd')
            ->add('quantite')
            ->add('description')
            ->add('prix')
            ->add('enPromo', CheckboxType::class, [
                'label' => 'En promotion',
                'required' => false,
            ])
            ->add('dateDebutPromo', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                // configurez ici d'autres options de widget et de formatage selon vos besoins
            ])
            ->add('dateFinPromo', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                // configurez ici d'autres options de widget et de formatage selon vos besoins
            ])
            ->add('image',FileType::class, array('data_class' => null,'required' => false))
            ->add('Categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom_cat'
            ])
            ->add('prixApresPromo', NumberType::class, [
                'label' => 'Prix après promotion',
                'required' => false, // Selon vos besoins
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}

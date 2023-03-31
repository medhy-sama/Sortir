<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\rechercheSortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class,[
                'label'=> 'Campus ',
                'required'=>'false',
                'class'=>Campus::class,
                'empty_data'=>null,
                'choice_label'=>'nom',
                'mapped' => true,
                'attr'=>[
                    'placeholder' => '',
                ]

            ])
            ->add('q', TextType::class,[
                'label' =>false,
                'required' =>false,
                'mapped' => true,
                'attr' => [
                    'placeholder' => 'Rechercher',
                ]

            ])
            ->add('datemin', DateTimeType::class,[
                'label'=>'Entre ',
                'html5' => true,
                'widget' => 'single_text',
                'mapped' => true,
                'required'=>false,
            ])
            ->add('datemax', DateTimeType::class,[
                'label'=>'et ',
                'html5' => true,
                'widget' => 'single_text',
                'mapped' => true,
                'required' =>false,
            ])
            ->add('organisateur', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur.ice',
                'required' => false,
                'data'=>true,
                'mapped' => true,
            ])

            ->add('inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit.e',
                'required' => false,
                'data'=>true,
                'mapped' => true,
            ])
            ->add('nonInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit.e',
                'required' => false,
                'data'=>true,
                'mapped' => true,
            ])
            ->add('sortiePassee', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
                'data'=>false,
                'mapped' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => rechercheSortie::class,
        ]);
    }
}

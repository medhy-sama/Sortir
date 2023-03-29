<?php

namespace App\Form;


use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('campus', EntityType::class,[
                'label'=> 'Campus ',
                'required'=>'false',
                'class'=>Campus::class,
                'choice_label'=>'nom',
                'mapped' => false,

            ])
            ->add('q', TextType::class,[
                'label' =>false,
                'required' =>false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Rechercher',
                ]

            ])
            ->add('datemin', DateTimeType::class,[
                'label'=>'Entre ',
                'html5' => true,
                'widget' => 'single_text',
                'mapped' => false,
            ])
            ->add('datemax', DateTimeType::class,[
                'label'=>'et ',
                'html5' => true,
                'widget' => 'single_text',
                'mapped' => false,
            ])
            ->add('organisateur', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur.ice',
                'required' => false,
                'data'=>true,
                'mapped' => false,
            ])

            ->add('inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit.e',
                'required' => false,
                'data'=>true,
                'mapped' => false,
            ])
            ->add('pasIncrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit.e',
                'required' => false,
                'data'=>true,
                'mapped' => false,
            ])
            ->add('passee', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
                'data'=>false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

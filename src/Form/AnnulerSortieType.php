<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class AnnulerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('motif', null, [

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez inscrire le motif d\'annulation',
                    ]),
                    new NotNull([
                        'message' => 'Veuillez inscrire le motif d\'annulation',
                    ]),
                    new Regex([
                        'pattern' => '^[a-zA-Z0-9]+$',
                        'message' => 'Le motif d\'annulation n\'est pas valide',

                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

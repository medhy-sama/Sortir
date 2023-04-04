<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class UserType extends AbstractType
{
    #[SecurityAssert\UserPassword(
        message: 'Wrong value for your current password',
    )]
    protected $oldPassword;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('username')
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre mot de passe',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                    ]),
                ],
                'invalid_message' => 'Attention les valeurs des champs de mot de passe ne correspondent pas.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer mot de passe'],

            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom  : '
                ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom  : '
            ])
            ->add('telephone', IntegerType::class, [
                'label' => 'Téléphone  : '
            ])
            ->add('mail', TextType::class, [
                'label' => 'Nom  : '
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo de Profil     : ',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/jpg',
                                'image/gif',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid IMG document',
                        ])
                    ],
                            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}


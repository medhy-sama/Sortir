<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        $builder
            ->add('nom', null,[
                'label'=> 'Titre de la sortie',
            ])
            ->add('datedebut', null,
            [
                'label' => 'Date de début',
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('duree', null,[
                'label'=> 'Durée de la sortie',
            ])
            ->add('datecloture', null,
                [
                    'label' => 'Date de fin d\'inscriptions',
                    'html5' => true,
                    'widget' => 'single_text',
                ])
            ->add('nbinscriptionsmax', null,[
                'label'=> 'Nombre de participants max ',
            ])
            ->add('descriptioninfos', null,[
                'label'=> 'Description',
            ])
            ->add('campus', EntityType::class,
                ['class'=> Campus::class,
                    'label'=> 'Campus',
                    'choice_label' => 'nom',
                    'placeholder' => '',
                   ])
            ->add('ville' , EntityType::class,
                [
                    'label'=>'Ville',
                    'class'=> Ville::class,
                    'choice_label'=> 'nomVille',
                    'placeholder' => '',
                ])

            ->add('lieu', EntityType::class,[

                'class'=>Lieu::class,
                'query_builder' => function(LieuRepository $lr) {
                    return $lr->createQueryBuilder('l')
                        ->join('l.ville','v');

                },
                'choice_label'=>'nomLieu',
            ])


//        $formModifier = function (FormInterface $form, Ville $ville = null) {
//            $lieux = null === $ville ? [] : $ville->getLieux();
//
//            $form->add('lieu', EntityType::class, [
//                'class' => Lieu::class,
//                'placeholder' => '',
//                'choices' => $lieux,
//            ]);
//        };
//
//        $builder->addEventListener(
//            FormEvents::PRE_SET_DATA,
//            function (FormEvent $event) use ($formModifier) {
//                // this would be your entity, i.e. SportMeetup
//                $data = $event->getData();
//
//                $formModifier($event->getForm(), $data->getVille());
//            }
//        );
//
//        $builder->get('ville')->addEventListener(
//            FormEvents::POST_SUBMIT,
//            function (FormEvent $event) use ($formModifier) {
//                // It's important here to fetch $event->getForm()->getData(), as
//                // $event->getData() will get you the client data (that is, the ID)
//                $sport = $event->getForm()->getData();
//
//                // since we've added the listener to the child, we'll have to pass on
//                // the parent to the callback function!
//                $formModifier($event->getForm()->getParent(), $sport);
//            }
//        )

             ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound'=> true,
            'data_class' => Sortie::class,
        ]);
    }
}

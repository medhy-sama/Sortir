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
                   ])


            ->add('lieu' , EntityType::class,
                [
                    'label'=>'Lieu',
                    'class'=> Lieu::class,
                    'choice_label' => 'nom_lieu',
                   ])

            ->add('ville' , EntityType::class,
                [
                    'label'=>'Ville',
                    'class'=> Ville::class,
                    'choice_label'=> 'nomVille',
//                    'query_builder' => function(CampusRepository $cr) {
//                        return $cr->createQueryBuilder('c')
//                            ->join('c.ville', 'v');
//                    }
                        ]) ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

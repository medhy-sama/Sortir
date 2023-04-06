<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;

use Doctrine\DBAL\Types\IntegerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class SortieType extends AbstractType
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Titre de la sortie   : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner le nom de la sortie',
                    ]),
                    new NotNull([
                        'message' => 'Veuillez renseigner le nom de la sortie',
                    ]),
                ],
            ])
            ->add('datedebut', null,
                [
                    'label' => 'Date de début   : ',
                    'html5' => true,
                    'widget' => 'single_text',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez renseigner la date de la sortie',
                        ]),
                        new NotNull([
                            'message' => 'Veuillez renseigner la date de la sortie',
                        ]),
                        new GreaterThanOrEqual([
                            'value' => 'today',
                            'message' => 'La date de début de l\'évènement n\'est pas valide',
                        ])
                    ]
                ])
            ->add('duree', null, [
                'label' => 'Durée de la sortie   : ',
                'attr' => [
                    'placeholder' => 'en minutes',
                    'min' => 1,
                    'max' => 2880
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner la duree de la sortie',
                    ]),
                    new NotNull([
                        'message' => 'Veuillez renseigner la duree de la sortie',
                    ]),
                    new Positive([
                        'message' => 'La durée doit être supérieur à 0'
                    ]),
                    new Type([
                        'type' => 'integer',
                        'message' => 'La durée de la sortie doit être un entier et renseignée en minutes',
                    ])
                ]
            ])
            ->add('datecloture', null,
                [
                    'label' => 'Date de fin d\'inscriptions : ',
                    'html5' => true,
                    'widget' => 'single_text',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez renseigner la date de cloture des inscriptions à la sortie',
                        ]),
                        new NotNull([
                            'message' => 'Veuillez renseigner la date de cloture des inscriptions à la sortie',
                        ]),
                        new GreaterThanOrEqual([
                            'value' => 'today',
                            'message' => 'La date de début de l\'évènement n\'est pas valide',
                        ]),
                        new LessThan([
                            'propertyPath' => 'parent.all [datedebut].data',
                            'message' => 'La date de clôture doit être avant la date de début de l\'évènement'
                        ])
                    ]
                ])
            ->add('nbinscriptionsmax', null, [
                'label' => 'Nombre de participants max   : ',
                'attr' => [
                    'min' => 1,
                    'max' => 150
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner le nombre de participants maximum',
                    ]),
                    new NotNull([
                        'message' => 'Veuillez renseigner le nombre de participants maximum',
                    ]),
                    new Positive([
                        'message' => 'La durée doit être supérieur à 0'
                    ]),
                ]
            ])
            ->add('descriptioninfos', null, [
                'label' => 'Description  : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez décrire la sortie',
                    ]),
                    new NotNull([
                        'message' => 'Veuillez décrire la sortie',
                    ]),
                    new Regex([
                        'pattern' => '^[a-zA-Z0-9]+$',

                    ])
                ]
            ])
            ->add('campus', EntityType::class,
                ['class' => Campus::class,
                    'label' => 'Campus   : ',
                    'choice_label' => 'nom',
                    'disabled' => true,
                ]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    /**
     * @param FormInterface $form
     * @param Ville|null $ville
     * @return void
     * Ajout des champs villes et lieux
     */
    public function addElements(FormInterface $form, Ville $ville = null)
    {
        $form->add('ville', EntityType::class,
            [
                'label' => 'Ville : ',
                'class' => Ville::class,
                'choice_label' => 'nomVille',
                'placeholder' => 'Choisir une ville ',
                'required' => true,
                'data' => $ville,
            ]);
        $lieu = array();

        if ($ville) {
            $repoLieu = $this->em->getRepository(Lieu::class);

            $lieu = $repoLieu->createQueryBuilder("q")
                ->where("q.ville = :villeId")
                ->setParameter("villeId", $ville->getId())
                ->getQuery()
                ->getResult();
        }

        $form->add('lieu', EntityType::class,
            [
                'class' => Lieu::class,
                'required' => true,
                'label' => 'Lieu    : ',
                'choice_label' => 'nomLieu',
                'choices' => $lieu,
                'placeholder' => 'Choisir une ville d\'abord'
            ]);
    }

    /**
     * @param FormEvent $event
     * @return void
     * Methode qui permet de faire un évènement sur le champ ville avant de soumettre le formulaire.
     */
    function onPreSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();

        $ville = $this->em->getRepository(Ville::class)->find($data['ville']);

        $this->addElements($form, $ville);
    }

    /**
     * @param FormEvent $event
     * @return void
     * Methode qui permet de set les datas du champ ville.
     */
    function onPreSetData(FormEvent $event): void
    {
        $sortie = $event->getData();
        $form = $event->getForm();

        $ville = $sortie->getVille() ? $sortie->getVille() : null;

        $this->addElements($form, $ville);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => true,
            'data_class' => Sortie::class,
        ]);
    }
}

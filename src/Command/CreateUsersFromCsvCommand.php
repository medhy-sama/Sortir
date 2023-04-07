<?php

namespace App\Command;

use App\Entity\Campus;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Nom de la commande et description !
 */
#[AsCommand(
    name: 'app:CreateUsersFromCsv',
    description: 'Add a short description for your command',
)]
class CreateUsersFromCsvCommand extends Command
{
    /**
     * @var EntityManagerInterface
     * @var string
     * @var SymfonyStyle (affichage console)
     * @var UserRepository
     * @var UserPasswordHasherInterface
     */
    public EntityManagerInterface $entityManager;
    private string $dataDirectory;
    private SymfonyStyle $io;

    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        EntityManagerInterface      $entityManager,
        string                      $dataDirectory,
        UserRepository              $userRepository,
        UserPasswordHasherInterface $userPasswordHasher
    )
    {
        parent::__construct();
        $this->dataDirectory = $dataDirectory;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @return void
     * Set de la description
     * Ajout de l'argument nécessaire pour enregistrer le fichier csv
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Importer des données d\'un fichier CSV')
            ->addArgument('name', InputArgument::REQUIRED);
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * Execute la commande
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createUsers($input->getArgument('name'));
        return Command::SUCCESS;
    }

    /**
     * @param $name
     * @return array
     * Fonction qui permet de récupérer le fichier csv et de le decoder
     */
    private function getDataFromFile($name): array
    {
        $file = $this->dataDirectory . $name;

        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

        $normalizer = [new ObjectNormalizer()];

        $encoders = [
            new CsvEncoder(),
            new XmlEncoder(),
            new JsonEncoder()
        ];

        //Le composant Serializer est destiné à être utilisé pour transformer des objets dans un format spécifique (XML, JSON, YAML, ...) et inversement.
        $serializer = new Serializer($normalizer, $encoders);

        /**@var string $fileString */
        $fileString = file_get_contents($file);

        /**
         * @var $fileString
         * @var $fileExtension
         * Serializer va -> decode to array
         */
        $data = $serializer->decode($fileString, $fileExtension);

        return $data;
    }

    private function createUsers($name): void
    {
        // Permet de faire un affichage dans la console
        $this->io->section('creation des utilisateurs a partir du fichier csv');
        // Incrément pour savoir combien de lignes ont été rajoutées en base.
        $usersCreated = 0;
        // Boucle sur ma variable data qui est un tableau d'utilisateur pour créer les users.
        foreach ($this->getDataFromFile($name) as $row) {
            //Verifier l'existence de l'utilisateur en base de données.
            if (array_key_exists('username', $row) && !empty($row['username'])) {
                $user = $this->userRepository->findOneBy([
                    'username' => $row['username']
                ]);
                // Traitement à faire si l'utilisateur n'existe pas en base de données.
                if (!$user) {
                    $user = new User();

                    $user->setUsername($row['username']);
                    $user->setCampus($this->entityManager->getRepository(Campus::class)->find($row['campus_id']));
                    $user->setPassword($this->userPasswordHasher->hashPassword($user, $row['password']));
                    $user->setNom($row['nom']);
                    $user->setPrenom($row['prenom']);
                    $user->setTelephone($row['telephone']);
                    $user->setMail($row['mail']);
                    $user->setAdministrateur($row['administrateur']);
                    $user->setActif($row['actif']);
                    $user->setIsVerified($row['is_verified']);

                    $this->entityManager->persist($user);
                    $usersCreated++;
                }
            }
        }
        $this->entityManager->flush();
        //Condition qui me permet de faire un affichage console avec le nombre de lignes ajouté en base de données.
        if ($usersCreated > 1) {
            $string = "{$usersCreated} utilisateurs crees en base de données.";
        } elseif ($usersCreated === 1) {
            $string = "1 utilisateur cree en base de données.";
        } else {
            $string = "aucun utilisateur cree en base de données.";
        }
        $this->io->success($string);
    }
}

<?php

namespace App\Command;

use App\Entity\Campus;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsCommand(
    name: 'app:CreateUsersFromCsv',
    description: 'Add a short description for your command',
)]
class CreateUsersFromCsvCommand extends Command
{
    public EntityManagerInterface $entityManager;
    private string $dataDirectory;
    private SymfonyStyle $io;

    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $dataDirectory,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher
    )
    {
        parent::__construct();
        $this->dataDirectory = $dataDirectory;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }


    protected function configure(): void
    {
        $this
            ->setDescription('Importer des données d\'un fichier CSV')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

      $this->createUsers();

        return Command::SUCCESS;
    }

    private function getDataFromFile() : array
    {
        $file = $this->dataDirectory.'create-user.csv';

        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

        $normalizer = [new ObjectNormalizer()];

        $encoders = [
            new CsvEncoder(),
            new XmlEncoder(),
            new JsonEncoder()
        ];

        $serializer = new Serializer($normalizer, $encoders);

        /**@var string $fileString */
        $fileString = file_get_contents($file);

        $data = $serializer->decode($fileString, $fileExtension);


        return $data;

    }

    private function createUsers(): void
    {

        $this->io->section('creation des utilisateurs a partir du fichier csv');
        $usersCreated = 0;
        foreach ($this->getDataFromFile() as $row) {

            if (array_key_exists('username', $row) && !empty($row['username'])){
                $user = $this->userRepository->findOneBy([
                    'username' => $row['username']
                    ]);
                if (!$user){
                    $user = new User();

                    $user->setUsername($row['username']);
                    $user->setCampus($this->entityManager->getRepository(Campus::class)->find($row['campus_id']));
//                    $user->setRoles($row['role']);
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

        if ($usersCreated > 1 ){
            $string = "{$usersCreated} utilisateurs crees en base de données.";
        }elseif ($usersCreated === 1){
            $string = "1 utilisateur cree en base de données.";
        }else{
            $string = "aucun utilisateur cree en base de données.";
        }
        $this->io->success($string);
    }
}

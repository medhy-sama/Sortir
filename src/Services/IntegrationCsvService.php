<?php

namespace App\Services;

use App\Entity\Campus;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Voir commentaires de la command 'CreateUsersFromCsvCommand.php' pour plus de détails.
 */
class IntegrationCsvService
{
    private SymfonyStyle $io;
    private string $dataDirectory;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    public EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface      $entityManager,
        string                      $dataDirectory,
        UserRepository              $userRepository,
        UserPasswordHasherInterface $userPasswordHasher
    )
    {
        $this->dataDirectory = $dataDirectory;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function getDataFromFile($name): array
    {
        $file = $this->dataDirectory . $name;

        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

        $normalizer = [new ObjectNormalizer()];
        $encoders = [
            new CsvEncoder(),
            new XmlEncoder(),
            new JsonEncoder()
        ];

        $serializer = new Serializer($normalizer, $encoders);
        $fileString = file_get_contents($file);
        $data = $serializer->decode($fileString, $fileExtension);

        return $data;

    }

    public function createUsers($name): void
    {
        dump($name);
        $usersCreated = 0;
        foreach ($this->getDataFromFile($name) as $row) {

            if (array_key_exists('username', $row) && !empty($row['username'])) {
                $user = $this->userRepository->findOneBy([
                    'username' => $row['username']
                ]);
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
    }
}
<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\rechercheSortie;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function search(rechercheSortie $recherche, User $user, Etat $etatpasse): array
    {
        $sorties= $this->createQueryBuilder('s');


        if($recherche->getNoninscrit()){

             $sorties
                 ->leftJoin('s.inscriptions', 'i', 'WITH', 'i.user_id = :user')
                 ->andWhere('i.id IS NULL')
                 ->setParameter('user', $user->getId());
        }


            if($recherche->getOrganisateur()){
                $sorties
                    ->orWhere('s.organisateur = :organisateur')
                    ->setParameter('organisateur',$user);

                 }


            if($recherche->getInscrit()){
                $sorties
                    ->leftJoin('s.inscriptions', 'insc')
                    ->orWhere('insc.user_id = :user')
                    ->setParameter('user',$user->getId());
            }


            if(!empty ($recherche->getSortiepassee())){
                $sorties
                    ->orWhere('s.etat = :passee')
                    ->setParameter('passee', $etatpasse);

            }


            if(!empty($recherche->getQ())){
                $sorties
                    ->andWhere('s.nom LIKE :searchTerm')
                    ->setParameter('searchTerm', '%'.($recherche->getQ()).'%');


            }

            if(!empty ($recherche->getCampus())){
                $sorties
                    ->andWhere('s.campus = :campus')
                    ->setParameter('campus', $recherche->getCampus());


            }

            if(!empty($recherche->getDatemin())){
                $sorties
                    ->andWhere('s.datedebut >= :datemin')
                    ->setParameter('datemin',$recherche->getDatemin());


            }

            if(!empty($recherche->getDatemax())){
                $sorties
                    ->andWhere('s.datedebut <= :datemax')
                    ->setParameter('datemax',$recherche->getDatemax());

            }
            $sorties
                ->orderBy('s.datedebut','ASC')
                ->andWhere('s.etat != 7');
        return $sorties ->getQuery()->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Etat;
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

    public function search(rechercheSortie $recherche, User $user, Etat $etatpasse) :array
    {
            $sorties= $this->createQueryBuilder('s');

            if(!empty($recherche->getQ())){
                $sorties = $sorties
                    ->andWhere('s.nom LIKE :searchTerm')
                    ->setParameter('searchTerm', '%'.($recherche->getQ()).'%')
                    ->orderBy('s.datedebut','DESC');


            }

            if(!empty ($recherche->getCampus())){
                $sorties = $sorties
                    ->andWhere('s.campus = :campus')
                    ->setParameter('campus', $recherche->getCampus())
                    ->orderBy('s.datedebut','ASC');

            }

            if(!empty($recherche->getDatemin())){
                $sorties = $sorties
                    ->andWhere('s.datedebut >= :datemin')
                    ->setParameter('datemin',$recherche->getDatemin())
                    ->orderBy('s.datedebut','ASC');

            }

            if(!empty($recherche->getDatemax())){
                $sorties = $sorties
                    ->andWhere('s.datedebut <= :datemax')
                    ->setParameter('datemax',$recherche->getDatemax())
                    ->orderBy('s.datedebut','ASC');

            }

            if($recherche->getOrganisateur()){
                $sorties = $sorties
                    ->orWhere('s.organisateur = :organisateur')
                    ->setParameter('organisateur',$user)
                    ->orderBy('s.datedebut','ASC');
            }

            if($recherche->getInscrit()){
                $sorties = $sorties
                    ->orWhere(':user MEMBER OF s.inscriptions')
                    ->setParameter('user',$user)
                    ->orderBy('s.datedebut','ASC');
            }

            if($recherche->getNoninscrit()){
                $sorties = $sorties
                    ->orWhere(':user NOT MEMBER OF s.inscriptions')
                    ->setParameter('user',$user)
                    ->orderBy('s.datedebut','ASC');
            }

            if(!empty ($recherche->getSortiepassee())){
                $sorties = $sorties
                    ->orWhere('s.etat = :passee')
                    ->setParameter('passee', $etatpasse)
                    ->orderBy('s.datedebut','ASC');
            }


        return $sorties ->getQuery()->getResult();
    }
}

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


            $sorties= $this->createQueryBuilder('s')
                            ->leftjoin('s.inscriptions','i');
//                            ->andWhere('s.etat != 7');


//        select * from sortie where id NOT IN (SELECT sortie_id_id from inscription where user_id_id =3)












//                $dql = "SELECT s  WHERE user_id = :user ";
//            if($recherche->getNoninscrit()){
////                $sorties
//
//                    $sorties->where($sorties->expr()->notIn('s.id', function($subQueryBuilder) :array {
//                        $subQueryBuilder->select('i.sortie_id_id')
//                            ->from('App\Entity\Inscription', 'i')
//                            ->where('i.user_id_id = :user_id');
//                    }))
//                        ->setParameter('user', $user->getId());
////                    ->orWhere($sorties->expr()->notIn('s.id', $sorties))
//
////                        function ($subsorties){
////                                $subsorties -> select('i.sortie_id_id')
////                            -> from('i')
////                            -> where('i.user_id_id = :user');
////
////                    }))
////                        ->setParameter('user',$user->getId());
//
//
//            }

            if($recherche->getOrganisateur()){
                $sorties
                    ->orWhere('s.organisateur = :organisateur')
                    ->setParameter('organisateur',$user);

                 }


            if($recherche->getInscrit()){
                $sorties
                    ->orWhere('i.user_id = :user')
                    ->setParameter('user',$user->getId());


            }

//            if($recherche->getNoninscrit()){
//                $subquery = $sorties
//                    ->select('i.sortie_id')
//                    ->where('i.user_id = :user')
//                    ->setParameter('user', $user->getId());
//                $sorties-> orWhere($sorties->expr()->notIn('s.id',$subquery));
//            }

            if($recherche->getNoninscrit()){
//                $sorties
//                    ->orWhere($sorties->expr()->notIn('s.id', $dql));
                $sousSorties = $sorties -> where( $sorties ->expr()->eq('i.user_id',':user'));


                $sorties = $sorties ->select('s')

                    ->from(Sortie::class,'s')
                    ->where($sorties->expr()->notIn('i.user_id',$sousSorties));
//                    ->getQuery()->getResult();

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
            $sorties ->orderBy('s.datedebut','ASC');
        return $sorties ->getQuery()->getResult();
    }
}

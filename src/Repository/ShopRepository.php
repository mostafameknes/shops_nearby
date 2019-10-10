<?php

namespace App\Repository;

use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Shop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shop[]    findAll()
 * @method Shop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Shop::class);
    }

    public function notMyPreferred($user){
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
		$rsm->addRootEntityFromClassMetadata('App\Entity\Shop', 'u');
	    $q=$this->getEntityManager()
	        ->createNativeQuery('SELECT * FROM Shop u WHERE u.id NOT IN (SELECT shop_id FROM UserPreference WHERE user_id='.$user.')',$rsm)
	    	->getResult();
	    return $q;
    }
}

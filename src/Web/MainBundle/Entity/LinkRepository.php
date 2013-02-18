<?php

namespace Web\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * LinkRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LinkRepository extends EntityRepository
{
	public function getUserLink($user)
	{
		$qb = $this->createQueryBuilder('a')
					->join('a.user', 'c', 'WITH', 'c.id = '.$user->getId());
		return $qb->getQuery()->getResult();
	}

}
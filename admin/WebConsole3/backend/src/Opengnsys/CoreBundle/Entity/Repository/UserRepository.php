<?php

/*
 * This file is part of the Opengnsys Project package.
 *
 * Created by Miguel Angel de Vega Alcantara on 01/10/18. <miguelangel.devega@sic.uhu.es>
 * Copyright (c) 2015 Opengnsys. All rights reserved.
 *
 */

namespace Opengnsys\CoreBundle\Entity\Repository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends BaseRepository
{
    public function findByObservable($term = "", $limit = null, $offset = null, $ordered = array(), $selects = array(), $searchs = array(), $matching = array())
    {
        $qb = $this->createQueryBuilder('o');

        if(count($selects) > 0){
            $qb = $this->createSelect($qb, $selects);
        }else{
            $qb->select("DISTINCT o.username, o.usernameCanonical, o.email, o.emailCanonical, o.enabled, o.salt, o.password, o.lastLogin, o.confirmationToken, o.passwordRequestedAt, o.roles, o.firstname, o.lastname, o.locale, o.timezone, o.createdAt, o.updatedAt, o.name, o.profile, o.id");
        }

        if($term != ""){
            if(count($searchs) > 0){
                $qb = $this->createSearch($qb, $term, $searchs);
            }else{
                $qb->andWhere("o.username LIKE :term OR o.usernameCanonical LIKE :term OR o.email LIKE :term OR o.emailCanonical LIKE :term OR o.enabled LIKE :term OR o.salt LIKE :term OR o.password LIKE :term OR o.lastLogin LIKE :term OR o.confirmationToken LIKE :term OR o.passwordRequestedAt LIKE :term OR o.roles LIKE :term OR o.firstname LIKE :term OR o.lastname LIKE :term OR o.locale LIKE :term OR o.timezone LIKE :term OR o.createdAt LIKE :term OR o.updatedAt LIKE :term OR o.name LIKE :term OR o.profile LIKE :term OR o.id LIKE :term ")->setParameter('term', '%' . $term . '%');
            }
        }

        $qb = $this->createMaching($qb, $matching);

        $qb = $this->createOrdered($qb, $ordered);

        if($limit != null){
            $qb->setMaxResults($limit);
        }

        if($offset){
            $qb->setFirstResult($offset);
        }

        $entities = $qb->getQuery()->getScalarResult();
        return $entities;
    }

    public function countFiltered($term = "", $searchs = array(), $matching = array())
    {
        $qb = $this->createQueryBuilder('o');

        $qb->select("count(DISTINCT o.id)");

        if($term != ""){
            if(count($searchs) > 0){
                $qb = $this->createSearch($qb, $term, $searchs);
            }else{
                $qb->andWhere("o.username LIKE :term OR o.usernameCanonical LIKE :term OR o.email LIKE :term OR o.emailCanonical LIKE :term OR o.enabled LIKE :term OR o.salt LIKE :term OR o.password LIKE :term OR o.lastLogin LIKE :term OR o.confirmationToken LIKE :term OR o.passwordRequestedAt LIKE :term OR o.roles LIKE :term OR o.firstname LIKE :term OR o.lastname LIKE :term OR o.locale LIKE :term OR o.timezone LIKE :term OR o.createdAt LIKE :term OR o.updatedAt LIKE :term OR o.name LIKE :term OR o.profile LIKE :term OR o.id LIKE :term ")->setParameter('term', '%' . $term . '%');
            }
        }

        $qb = $this->createMaching($qb, $matching);

        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }
}
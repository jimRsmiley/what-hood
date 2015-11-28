<?php

namespace Whathood\Mapper;

use Whathood\Entity\DefaultJob;

/**
 * handle interactions between the object model jobs and the database
 */
class QueueMapper extends BaseMapper {

    /*
     * fetch all neighborhoods, ALL of them
     */
    public function fetchAll() {
        $qb = $this->em->createQueryBuilder();
        $qb->select('q')
            ->from('Whathood\Entity\DefaultJob','q');
        return $qb->getQuery()->getResult();
    }
}
?>

<?php

namespace Whathood\Service;

use Whathood\Entity\UserPolygon;

class MessagingService {

    protected $_emailer;

    public function setEmailer($emailer) {
        $this->_emailer = $emailer;
    }

    public function getEmailer() {
        return $this->_emailer;
    }

    public function __construct(array $data = null) {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
        $hydrator->hydrate($data, $this);
    }

    public static function build(array $data) {
        $messenger = new \Whathood\Service\MessagingService($data);
        return $messenger;
    }

    public function notifyUserNeighborhoodAdd(UserPolygon $userPolygon) {
        $this->getEmailer()->send(
            sprintf("New %s Neigborhood Added",
                $userPolygon->getNeighborhood()->getName()),
            sprintf("by user %s",
                $userPolygon->getWhathoodUser()->__toString())
        );
    }
}

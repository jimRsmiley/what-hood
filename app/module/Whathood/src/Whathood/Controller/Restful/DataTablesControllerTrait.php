<?php

namespace Whathood\Controller\Restful;

use Zend\View\Model\JsonModel;

trait DataTablesControllerTrait {

    protected $_start;
    protected $_length;
    protected $_draw;
    protected $_pageNum;
    protected $_paginator;

    abstract public function getQuery();

    public function getCurrentItems() {
        $this->_start = parent::params()->fromQuery('start');
        $this->_length = parent::params()->fromQuery('length');
        $this->_draw = parent::params()->fromQuery('draw');
        if (0 == $this->_length)
            $this->_pageNum = 1;
        else
            $this->_pageNum = $this->_start / $this->_length + 1;
        $query = $this->getQuery();
        $this->_paginator = new \Whathood\View\Paginator\Paginator(
                        new \Whathood\View\Paginator\PaginatorAdapter($query)
                );
        $this->_paginator->setDefaultItemCountPerPage($this->_length);
        $this->_paginator->setCurrentPageNumber($this->_pageNum);
        return $this->_paginator->getCurrentItems();
    }

    public function dataTablesAction() {
        return $this->getJsonModel($this->getCurrentItems());
    }

    public function getJsonModel($objects) {
        $data = array();
        foreach ($objects as $obj) {
            array_push($data, $obj->toArray());
        }
        return new JsonModel(array(
            'data'              => $data,
            "recordsTotal"      => $this->_paginator->getTotalItemCount(),
            "recordsFiltered"   => $this->_paginator->getTotalItemCount()
        ));
    }
}

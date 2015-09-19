<?php

namespace Whathood\Controller;

trait ControllerMapperTrait {

    private $_mapper_builder;

    public function m() {
        if ($this->_mapper_builder == null)
            $this->_mapper_builder = $this->getServiceLocator()
                ->get('Whathood\Mapper\Builder');
        return $this->_mapper_builder;
    }
}

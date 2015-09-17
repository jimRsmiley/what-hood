<?php

namespace Whathood\View\Paginator;

/**
 * Description of NeighborhoodPaginator
 */
class PaginatorAdapter implements \Zend\Paginator\Adapter\AdapterInterface
{
    /**
     * @var Doctrine\ORM\NativeQuery
     */
    protected $query;
    protected $count;


    /**
     * @param Doctrine\ORM\NativeQuery $query
     */
    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * Returns the total number of rows in the result set.
     *
     * @return integer
     */
    public function count()
    {
        if(!$this->count)
        {
            //change to a count query by changing the bit before the FROM
            $sql = explode(' FROM ', $this->query->getSql());
            $sql[0] = 'SELECT COUNT(*)';
            $sql = implode(' FROM ', $sql);

            $paramArray = $this->query->getParameters();

            $columns = array();
            foreach( $paramArray as $param )
                $columns[] = $param->getValue();

            $db = $this->query->getEntityManager()->getConnection();

            $this->count = (int) $db->fetchColumn(
                        $sql,
                        $columns
                    );
        }

        return $this->count;
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $cloneQuery = clone $this->query;

        $cloneQuery->setParameters($this->query->getParameters());

        foreach($this->query->getHints() as $name => $value)
        {
            $cloneQuery->setHint($name, $value);
        }

        //add on limit and offset
        $cloneQuery->setMaxResults($itemCountPerPage);
        $cloneQuery->setFirstResult($offset*$itemCountPerPage);

        return $cloneQuery->getResult();
    }

}

?>

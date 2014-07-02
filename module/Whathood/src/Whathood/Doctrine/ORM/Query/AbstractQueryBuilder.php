<?php
namespace Application\Doctrine\ORM\Query;
/**
 * Description of AbstractQueryBuilder
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class AbstractQueryBuilder {
    protected $qb;
    
    protected $whereStrings = null;
    protected $params = null;
    
    public function getSql() {
        return $this->qb->getQuery()->getSql();
    }
    
    public function addWhereString( $str, $key = null, $value = null ) {
        if( $this->whereStrings == null )
            $this->whereStrings = array();
        
        $this->whereStrings[] = $str;
   }
    
    public function addParameter( $key, $value ) {
        if( $this->params == null )
            $this->params = array();
        
        $this->params[$key] = $value;
    }
    
    public function setWhereStrings() {
        
        if( $this->whereStrings == null ) {
            return;
        }
        
        $whereStr = implode( " AND ", $this->whereStrings );
        $this->qb->where( $whereStr );
        
        foreach( $this->params as $key => $value ) {
            $this->qb->setParameter( $key,$value);
        }
    }
    
    public function setMaxResults($int) {
        $this->qb->setMaxResults($int);
    }
}

?>

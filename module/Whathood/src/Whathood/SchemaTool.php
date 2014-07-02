<?php
namespace Whathood;
/**
 * Description of Schema
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class SchemaTool {
    
    protected $sm = null;
    protected $em = null;
    protected $autoIncrementNeighborhoodIds = true;
    
    public function __construct($sm) {
        $this->sm = $sm;
        $this->em = $sm->get('doctrine.entitymanager.orm_default');
    }
    
    public function setAutoIncrementNeighborhoodIds($bool) {
        $this->autoIncrementNeighborhoodIds = $bool;
    }
    
    public function neighborhoodsToFile($file) {
        $json = \Whathood\Entity\Neighborhood
                                    ::neighborhoodsToJson( $neighborhoods );
        file_put_contents($file, $json );
    }
    
    public function neighborhoodsFromFile($file) {
        $json = file_get_contents($file);

        return \Whathood\Entity\Neighborhood::jsonToNeighborhoods( $json );
    }
    public function backupDb($file) {
        $neighborhoodMapper = $this->sm->get('Whathood\Mapper\Neighborhood');

        $neighborhoods = $neighborhoodMapper->fetchAll();

        foreach( $neighborhoods as $n ) {
            print $n->getName()."\n";
        }
        $json = \Whathood\Entity\Neighborhood::neighborhoodsToJson( $neighborhoods );

        file_put_contents($file, $json );
    }
 
    public function addNeighborhoods( $neighborhoods ) {
        
        if( !$this->autoIncrementNeighborhoodIds ) {
            $metadata = $this->em->getClassMetaData(
                                            'Whathood\Entity\Neighborhood');
            $metadata->setIdGeneratorType(
                    \Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE );
        }
        
        $mapper = $this->sm->get('Whathood\Mapper\Neighborhood');
        foreach( $neighborhoods as $n ) {
            print getenv('APPLICATION_ENV') . " saving " 
                    . $n->getId() . " " . $n->getName() . "\n";
            $mapper->save($n);
        }
    }
    
    public function clearCache() {
        $cacheDriver = new \Doctrine\Common\Cache\ArrayCache();
        $deleted = $cacheDriver->deleteAll();
    }
    /*
     * connect to the db server and delete the database
     */
    public function dropSchema() {
        print "dropping schema\n";
        // Instantiate the schema tool
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);

        $factory = $this->em->getMetadataFactory();
        
        // Retrieve all of the mapping metadata
        $classes = $factory->getAllMetadata();

        // Delete the existing test database schema
        $tool->dropSchema($classes);
        print "schema dropped\n";
    }
    
    /*
     * connect to the db server and create the database schema
     */
    public function createSchema() {
        print "creating schema\n";
        // Instantiate the schema tool
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);

        $factory = $this->em->getMetadataFactory();
        
        // Retrieve all of the mapping metadata
        $classes = $factory->getAllMetadata();

        // Create the test database schema
        $tool->createSchema($classes);
        print "schema created\n";
    }
    

    
    function initAzevea( $sm, $region, $user ) {

        $azeveaFile = $sm->get('Whathood\Spatial\NeighborhoodJsonFile\Azevea' );

        $azeveaFile->getWhathoodUser( $user );
        $azeveaFile->setRegion( $region );

        $neighborhoods = $azeveaFile->getPolygon( 
                                    $file = '../draft/Philadelphia_neighborhoods.json' );

        $neighborhoodMapper = $sm->get('Whathood\Mapper\Neighborhood');
        foreach( $neighborhoods as $n ) {
            $n->setAuthority(true);
            $neighborhoodMapper->save($n);
            print "saved " 
                . $user->getName() 
                    . "'s " 
                    .  $n->getName() 
                    . " id="
                    . $n->getId()
                    . "\n";
        }    
    }

    function initUpenn( $sm, $region, $user ) {

        $uPennFile = $sm->get('Whathood\Spatial\NeighborhoodJsonFile\Upenn');


        $uPennFile->getWhathoodUser( $user );
        $uPennFile->setRegion( $region );

        $neighborhoods = $uPennFile->getPolygon( 
                                    $file = '../draft/nis_neighborhood.json' );

        $neighborhoodMapper = $sm->get('Whathood\Mapper\Neighborhood');
        foreach( $neighborhoods as $n ) {
            $neighborhoodMapper->save($n);
            print "saved " 
                . $user->getName() 
                    . "'s " 
                    .  $n->getName() 
                    . " id="
                    . $n->getId()
                    . "\n";
        }
    }
    
}

?>

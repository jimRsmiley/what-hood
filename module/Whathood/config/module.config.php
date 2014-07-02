<?php
namespace Whathood;
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            
            'region' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/:region_name[/]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'Region',
                        'action'        => 'show',
                    ),
                ),
            ),
            
            'neighborhood' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/:region_name/:neighborhood_name[/]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'HeatMap',
                        'action'        => 'show',
                    ),
                ),
            ),
            
            'address_search' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/a[/:region_name][/:address][/]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'Whathood',
                        'action'        => 'by-address',
                    ),
                ),
            ),
            
            'user_polygon_id' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/n/id/:user_polygon_id[/format/:format]',
                    'constraints' => array(
                        'region_name' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'user_polygon_id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'UserPolygon',
                        'action'        => 'by-id',
                    ),
                ),
            ),
            
            'user_polygon_add' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/n/add[/]',
                    'constraints' => array(
                        'region_name' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'neighborhood_id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'UserPolygon',
                        'action'        => 'add',
                    ),
                ),
            ),
            
            'neighborhood_edit' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/n/edit[/]',
                    'constraints' => array(
                        'region_name' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'neighborhood_id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'NeighborhoodPolygon',
                        'action'        => 'edit',
                    ),
                ),
            ),
            
            'user_polygon_page' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/n/page/:page[/center/:center]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller' => 'Whathood\Controller\UserPolygon',
                        'action'     => 'page',
                    ),
                ),
            ),

            'auth' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/auth/:action',
                    'constraints' => array(
                        'whathood_user_id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'Auth',
                    ),
                ),
            ),
            
            'user_by_name' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/u/:whathood_user_name[/]',
                    'constraints' => array(
                        'whathood_user_id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'WhathoodUser',
                        'action'        => 'by-user-name',
                    ),
                ),
            ),
            
            'user' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/user/:action',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'UserPolygon',
                        'action'        => 'by-user-id'
                    ),
                ),
            ),
            
            'region_default' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/r/:action',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'Region',
                    ),
                ),
            ),
            
            'whathood_search' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/whathood-search',
                    'constraints' => array(
                        'region' => '[a-zA-Z][a-zA-Z0-9_-]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'Whathood',
                        'action'        => 'by-position'
                    ),
                ),
            ),
            
            'search' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/search',
                    'constraints' => array(
                        'region' => '[a-zA-Z][a-zA-Z0-9_-]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'Search',
                        'action'        => 'index',
                    ),
                ),
            ),
            
            'heatmap' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/heatmap[/:controller][/:index]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller' => 'Whathood\Controller\HeatMap',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'constraints' => array(
                        'regionName'    => 'Philadelphia'
                    ),
                    'defaults' => array(
                        'controller'    => 'Whathood\Controller\Region',
                        'action'        => 'show',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'about' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'about',
                            'defaults' => array(
                                'controller'    => 'Whathood\Controller\Index',
                                'action' => 'about'
                            )
                        ),
                    )
                )
            ),
            
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'whathood_default' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/whathood',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            
            'mylogger' => function($sm) {
                $mylogger = new \Whathood\MyLogger(
                        $sm->get('logger'),
                        $sm->get('emailer') );
                return $mylogger;
            },
                    
            'logger' => function($sm) {
                $config = $sm->get('Config');
                $file = $config['whathood']['log']['logfile'];
                $outWriter = new \Zend\Log\Writer\Stream($file);
                $logger = new \Zend\Log\Logger;
                $logger->addWriter($outWriter);
                return $logger;
            },
                    
            'emailer' => function($sm) {
                $config = $sm->get('Config');
                $emailer = new \Whathood\Model\Email( 
                                        $config['whathood']['log']['email'] );
                return $emailer;
            },
                    
            'mydoctrineentitymanager'  => function($sm) {
                $em = $sm->get('doctrine.entitymanager.orm_default');
                return $em;
            },
                    
            'Whathood\Service\ErrorHandling' => function($sm) {
                $logger = $sm->get('mylogger');
                $service = new \Whathood\Service\ErrorHandling($logger,$sm);
                return $service;
            },
                    
            'Whathood\SchemaTool'  => function($sm) {
                return new \Whathood\SchemaTool($sm);
            },
                    
            'Whathood\Mapper\HeatMapMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Whathood\Mapper\NeighborhoodHeatMapMapper( $sm, $em );
                return $mapper;
            },
                    
            'Whathood\Mapper\NeighborhoodMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Whathood\Mapper\NeighborhoodMapper( $sm, $em );
                return $mapper;
            },
                    
               
            'Whathood\Mapper\RegionMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Whathood\Mapper\RegionMapper( $sm, $em );
                return $mapper;
            },
             
            'Whathood\Mapper\NeighborhoodPolygonVoteMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Whathood\Mapper\NeighborhoodPolygonVoteMapper($sm,$em);
                return $mapper;
            },
                    
            'Whathood\Mapper\UserPolygonMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                return new \Whathood\Mapper\UserPolygonMapper( $sm,$em );
            },
                    
            'Whathood\Mapper\WhathoodUserMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');

                $mapper = new \Whathood\Mapper\WhathoodUserMapper( $sm,$em );
                return $mapper;
            },
                    
            'Whathood\Mapper\HeatMapTestPointMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');

                $mapper = new \Whathood\Mapper\HeatMapTestPointMapper( $sm,$em );
                return $mapper;
            },
                    
            'Whathood\Spatial\NeighborhoodJsonFile\Azavea' => function($sm) {
                return new \Whathood\Spatial\NeighborhoodJsonFile\Azavea();
            },
            'Whathood\Spatial\NeighborhoodJsonFile\Upenn' => function($sm) {
                return new \Whathood\Spatial\NeighborhoodJsonFile\Upenn();
            },
                    
            'Whathood\Model\AuthenticationService' => function($sm) {
                $config = $sm->get('Config');
                $whathoodConfig = $config['whathood'];
                $auth = new \Whathood\Model\AuthenticationService($whathoodConfig['auth']);
                return $auth;
            },
                    
            'Whathood\Model\Heatmap\HeatMapBuilder' => function($sm) {
                $heatMapMapper = $sm->get('Whathood\Mapper\HeatMapMapper');
                return new \Whathood\Model\HeatMap\HeatMapBuilder(
                        $heatMapMapper);
            },
            
            'Whathood\Mapper\NeighborhoodPointStrengthOfIdentityMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Whathood\Mapper\NeighborhoodPointStrengthOfIdentityMapper( $sm,$em );
                return $mapper;
            },
                    
            'Whathood\Mapper\NeighborhoodPolygonMapper' => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Whathood\Mapper\NeighborhoodPolygonMapper( $sm, $em );
                return $mapper;
            },
                    
            'Whathood\Mapper\TestPointMapper' => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                return new \Whathood\Mapper\TestPointMapper($sm,$em);
            },
                    
            'Whathood\Model\NeighborhoodHeatMapPointBuilder' => function($sm) {
                return new \Whathood\Model\NeighborhoodHeatMapPointBuilder();
            },
                    
            'Whathood\Mapper\ContentiousPointMapper' => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                return new \Whathood\Mapper\ContentiousPointMapper($sm,$em);
            },
        ),
    ),
                    
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
                
    'controllers' => array(
        'invokables' => array(
            'Whathood\Controller\Index' => 'Whathood\Controller\IndexController',
            'Whathood\Controller\NeighborhoodPolygon' => 'Whathood\Controller\NeighborhoodPolygonController',
            'Whathood\Controller\Region' => 'Whathood\Controller\RegionController',
            'Whathood\Controller\WhathoodUser' => 'Whathood\Controller\WhathoodUserController',
            'Whathood\Controller\Whathood' => 'Whathood\Controller\WhathoodController',
            'Whathood\Controller\Auth' => 'Whathood\Controller\AuthController',
            'Whathood\Controller\HeatMap' => 'Whathood\Controller\HeatMapController',
            'Whathood\Controller\Search' => 'Whathood\Controller\SearchController',
            'Whathood\Controller\UserPolygon' => 'Whathood\Controller\UserPolygonController',
            'Whathood\Controller\TestPoint' => 'Whathood\Controller\TestPointController',
            'Whathood\Controller\ContentiousPoint' => 'Whathood\Controller\ContentiousPointController',
        ),
    ),
                
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    
    'view_helpers' => array(
        'invokables' => array(
            'leafletJSHelper'                   => 'Whathood\View\Helper\LeafletJSHelper',
            'userRegionUrlHelper'               => 'Whathood\View\Helper\UserRegionUrlHelper',
            'staticGoogleMapImageUrl'           => 'Whathood\View\Helper\StaticGoogleMapImageUrl',
            'mybreadcrumbs'                     => 'Whathood\View\Helper\BreadCrumbs',
            'isProductionEnvironment'           => 'Whathood\View\Helper\IsProductionEnvironment',
            'arrayToDoubleQuoteElementedCSV'    => 'Whathood\View\Helper\ArrayToDoubleQuoteElementedCSV',
            'showAddressSearchInLayout'         => 'Whathood\View\Helper\ShowAddressSearchInLayoutHelper',
            'whathoodResultSummary'             => 'Whathood\View\Helper\WhathoodResultSummaryHelper',
            'isNeighborhoodOwner'               => 'Whathood\View\Helper\IsNeighborhoodOwnerHelper',
            'neighborhoodPolygonDisqus'         => 'Whathood\View\Helper\NeighborhoodPolygonDisqus',
            'heatMapDisqus'         => 'Whathood\View\Helper\HeatMapDisqus'
        ),
        
        'factories' => array(
            'auth'    => function( $helperPluginManager ) {
                $serviceLocator = $helperPluginManager->getServiceLocator();
                $viewHelper = new \Whathood\View\Helper\Auth();
                $viewHelper->setServiceLocator($serviceLocator);
                return $viewHelper;
            },
        )
    ),
    
    // Doctrine config
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Whathood/Entity' )
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Whathood\Entity' => 'Whathood_driver'
                ),
                
            ),
        ),
        'configuration' => array(
            'orm_default' => array(
                'types' => array(
                    'geometry' => 'CrEOF\Spatial\DBAL\Types\GeometryType',
                    'polygon'  => 'CrEOF\Spatial\DBAL\Types\Geometry\PolygonType',
                    'point'    => 'CrEOF\Spatial\DBAL\Types\Geometry\PointType',
                ),
                'string_functions' => array(
                    'ST_Within'     => 'Whathood\Spatial\ORM\Query\AST\Functions\MySql\STWithin',
                    'ST_Point'      => 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STPoint',
                    'ST_SetSRID'    => 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STSetSRID'
                )
            )
        ),
        'connection' => array(
            'orm_default' => array(
                'doctrine_type_mappings' => array(            
                    'geometry' => 'geometry',
                    'polygon'  => 'polygon',
                    'point'    => 'point'
                ),
            )
        ),
    ),
);

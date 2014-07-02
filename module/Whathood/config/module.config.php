<?php
namespace Application;
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
                        '__NAMESPACE__' => 'Application\Controller',
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
                        '__NAMESPACE__' => 'Application\Controller',
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
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Whathood',
                        'action'        => 'by-address',
                    ),
                ),
            ),
            
            'neighborhood_id' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/n/id/:neighborhood_id[/format/:format]',
                    'constraints' => array(
                        'region_name' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'neighborhood_id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'NeighborhoodPolygon',
                        'action'        => 'by-id',
                    ),
                ),
            ),
            
            
            'neighborhood_vote' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/n/vote[/:action][/]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'NeighborhoodPolygonVote',
                    ),
                ),
            ),
            
            'neighborhood_add' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/n/add[/]',
                    'constraints' => array(
                        'region_name' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'neighborhood_id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'NeighborhoodPolygon',
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
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'NeighborhoodPolygon',
                        'action'        => 'edit',
                    ),
                ),
            ),
            
            'neighborhood_page' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/n/page/:page[/center/:center]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Application\Controller\NeighborhoodPolygon',
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
                        '__NAMESPACE__' => 'Application\Controller',
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
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'NeighborhoodPolygon',
                        'action'        => 'by-user-name',
                    ),
                ),
            ),
            
            'user' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/user/:action',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'WhathoodUser',
                    ),
                ),
            ),
            
            'region_default' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/r/:action',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Region',
                    ),
                ),
            ),
            
            'whathood_search' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/whathood',
                    'constraints' => array(
                        'region' => '[a-zA-Z][a-zA-Z0-9_-]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
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
                        '__NAMESPACE__' => 'Application\Controller',
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
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Application\Controller\HeatMap',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            

            
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
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
                $mylogger = new \Application\MyLogger(
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
                $emailer = new \Application\Model\Email( 
                                        $config['whathood']['log']['email'] );
                return $emailer;
            },
                    
            'mydoctrineentitymanager'  => function($sm) {
                $em = $sm->get('doctrine.entitymanager.orm_default');
                return $em;
            },
                    
            'Application\Service\ErrorHandling' => function($sm) {
                $logger = $sm->get('mylogger');
                $service = new \Application\Service\ErrorHandling($logger,$sm);
                return $service;
            },
                    
            'Application\SchemaTool'  => function($sm) {
                return new \Application\SchemaTool($sm);
            },
                    
            'Application\Mapper\HeatMapMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Application\Mapper\HeatMapMapper( $sm, $em );
                return $mapper;
            },
                    
            'Application\Mapper\NeighborhoodMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Application\Mapper\NeighborhoodMapper( $sm, $em );
                return $mapper;
            },
                    
            'Application\Mapper\NeighborhoodPolygonVoteMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Application\Mapper\NeighborhoodVoteMapper( $sm, $em );
                return $mapper;
            },
               
            'Application\Mapper\RegionMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Application\Mapper\RegionMapper( $sm, $em );
                return $mapper;
            },
             
            'Application\Mapper\NeighborhoodPolygonVoteMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Application\Mapper\NeighborhoodPolygonVoteMapper($sm,$em);
                return $mapper;
            },
                    
            'Application\Mapper\NeighborhoodPolygonMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                return new \Application\Mapper\NeighborhoodPolygonMapper( $sm,$em );
            },
                    
            'Application\Mapper\WhathoodUserMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');

                $mapper = new \Application\Mapper\WhathoodUserMapper( $sm,$em );
                return $mapper;
            },
                    
            'Application\Spatial\NeighborhoodJsonFile\Azavea' => function($sm) {
                return new \Application\Spatial\NeighborhoodJsonFile\Azavea();
            },
            'Application\Spatial\NeighborhoodJsonFile\Upenn' => function($sm) {
                return new \Application\Spatial\NeighborhoodJsonFile\Upenn();
            },
                    
            'Application\Model\AuthenticationService' => function($sm) {
                $config = $sm->get('Config');
                $whathoodConfig = $config['whathood'];
                $auth = new \Application\Model\AuthenticationService($whathoodConfig['auth']);
                return $auth;
            },
                    
            'Application\Model\Heatmap\HeatMapBuilder' => function($sm) {
                $heatMapMapper = $sm->get('Application\Mapper\HeatMapMapper');
                return new \Application\Model\HeatMap\HeatMapBuilder(
                        $heatMapMapper);
            }
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
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\NeighborhoodPolygon' => 'Application\Controller\NeighborhoodPolygonController',
            'Application\Controller\Region' => 'Application\Controller\RegionController',
            'Application\Controller\WhathoodUser' => 'Application\Controller\WhathoodUserController',
            'Application\Controller\Whathood' => 'Application\Controller\WhathoodController',
            'Application\Controller\Auth' => 'Application\Controller\AuthController',
            'Application\Controller\HeatMap' => 'Application\Controller\HeatMapController',
            'Application\Controller\Search' => 'Application\Controller\SearchController',
            'Application\Controller\NeighborhoodPolygonVote' => 'Application\Controller\NeighborhoodPolygonVoteController',
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
            'leafletJSHelper'                   => 'Application\View\Helper\LeafletJSHelper',
            'userRegionUrlHelper'               => 'Application\View\Helper\UserRegionUrlHelper',
            'staticGoogleMapImageUrl'           => 'Application\View\Helper\StaticGoogleMapImageUrl',
            'mybreadcrumbs'                     => 'Application\View\Helper\BreadCrumbs',
            'isProductionEnvironment'           => 'Application\View\Helper\IsProductionEnvironment',
            'arrayToDoubleQuoteElementedCSV'    => 'Application\View\Helper\ArrayToDoubleQuoteElementedCSV',
            'showAddressSearchInLayout'         => 'Application\View\Helper\ShowAddressSearchInLayoutHelper',
            'whathoodResultSummary'             => 'Application\View\Helper\WhathoodResultSummaryHelper',
            'isNeighborhoodOwner'               => 'Application\View\Helper\IsNeighborhoodOwnerHelper',
            'neighborhoodPolygonDisqus'         => 'Application\View\Helper\NeighborhoodPolygonDisqus',
            'heatMapDisqus'         => 'Application\View\Helper\HeatMapDisqus'
        ),
        
        'factories' => array(
            'auth'    => function( $helperPluginManager ) {
                $serviceLocator = $helperPluginManager->getServiceLocator();
                $viewHelper = new \Application\View\Helper\Auth();
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
                'paths' => array(__DIR__ . '/../src/Application/Entity' )
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Application\Entity' => 'Application_driver'
                ),
                
            ),
        ),
        'configuration' => array(
            'orm_default' => array(
                'types' => array(
                    'geometry' => 'CrEOF\Spatial\DBAL\Types\GeometryType',
                    #'polygon'  => 'Application\Spatial\DBAL\Types\PolygonType',
                    'polygon'  => 'CrEOF\Spatial\DBAL\Types\Geometry\PolygonType',
                    'point'    => 'CrEOF\Spatial\DBAL\Types\Geometry\PointType',
                ),
                'string_functions' => array(
                    'ST_Within' => 'Application\Spatial\ORM\Query\AST\Functions\MySql\STWithin',
                    'ST_Point'     => 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STPoint'
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

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

		/*  routes are processed in descending order, put the most important at the bottom!

		/whathood/admin
		/whathood/user
		/whathood/user-polygon/by-id/id/:id

		*/

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

			/**
			 *
			 * User Polygon
			 *
			 **/
            'user_polygon_id' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/whathood/user-polygon/by-id/:user_polygon_id[/format/:format]',
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

            'user_polygon_page_list' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/whathood/user-polygon/page-list/page/:page',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller' => 'Whathood\Controller\UserPolygon',
                        'action'     => 'page-list',
                    ),
                ),
            ),

            'user_polygon_page_list' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/whathood/user-polygon/page-list/page/:page',
			    ),
            ),

            'user_polygon_page' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/whathood/user-polygon/page/:page[/center/:center][/neighborhood_id/:neighborhood_id]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller' => 'Whathood\Controller\UserPolygon',
                        'action'     => 'page-list',
                    ),
                ),
			),

			/**
			 *
			 * Neighborhood
			 *
			 **/
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

            'admin' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'Admin',
                        'action'        => 'index',
                    ),
                ),
            ),
        ),
    ),


    'console' => array(
        'router' => array(
            'routes' => array(
                'up' => array(
                    'options' => array(
                        'route' => 'up',
                        'defaults' => array(
                            'controller' => 'Whathood\Controller\UserPolygon',
                            'action' => 'consoledefault'
                        )
                    )
                ),
                'watcher-router' => array(
                 //   'type' => 'simple',
                    'options' => array(
                        'route' => 'watcher',
                        'defaults' => array(
                            'controller' => 'Whathood\Controller\Watcher',
                            'action' => 'watch'
                        )
                    )
                )
            )
        )
    ),


    'service_manager' => array(
        'factories' => array(

            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',

            'Whathood\MyLogger' => function($sm) {
                return new \Whathood\MyLogger(
                        $sm->get('Whathood\Logger'),
                        $sm->get('emailer') );
            },

            'Whathood\Logger' => function($sm) {
                $config = $sm->get('Config');
                $file = $config['whathood']['log']['logfile'];
                $file_writer = new \Zend\Log\Writer\Stream($file);
                $console_writer = new \Zend\Log\Writer\Stream('php://output');
                $logger = new \Zend\Log\Logger;
                $logger->addWriter($file_writer);
                //$logger->addWriter($console_writer);
                return $logger;
            },

            /*
             * get the regular file logger and add the console writer
             * to it
             */
            'Whathood\ConsoleLogger' => function($sm) {
                $logger = $sm->get('Whathood\Logger');
                $console_writer = new \Zend\Log\Writer\Stream('php://output');
                $logger->addWriter($console_writer);
                return $logger;
            },

            'Whathood\Emailer' => function($sm) {
                $config = $sm->get('Config');
                $emailer = new \Whathood\Model\Email($config['whathood']['log']['email'] );
                return $emailer;
            },


            'mydoctrineentitymanager'  => function($sm) {
                $em = $sm->get('doctrine.entitymanager.orm_default');
                return $em;
            },

            'Whathood\SchemaTool'  => function($sm) {
                return new \Whathood\SchemaTool($sm);
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

            'Whathood\Mapper\UserPolygonMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                return new \Whathood\Mapper\UserPolygonMapper( $sm,$em );
            },

            'Whathood\Mapper\WhathoodUserMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');

                $mapper = new \Whathood\Mapper\WhathoodUserMapper( $sm,$em );
                return $mapper;
            },

            'Whathood\Spatial\NeighborhoodJsonFile\Azavea' => function($sm) {
                return new \Whathood\Spatial\NeighborhoodJsonFile\Azavea();
            },
            'Whathood\Spatial\NeighborhoodJsonFile\Upenn' => function($sm) {
                return new \Whathood\Spatial\NeighborhoodJsonFile\Upenn();
            },

            'Whathood\Mapper\NeighborhoodPolygonMapper' => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Whathood\Mapper\NeighborhoodPolygonMapper( $sm, $em );
                return $mapper;
            },
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Whathood\Controller\Watcher' => 'Whathood\Controller\WatcherController',
            'Whathood\Controller\Admin' => 'Whathood\Controller\AdminController',
            'Whathood\Controller\ContentiousPoint' => 'Whathood\Controller\ContentiousPointController',
            'Whathood\Controller\CreateEvent' => 'Whathood\Controller\CreateEventController',
            'Whathood\Controller\Index' => 'Whathood\Controller\IndexController',
            'Whathood\Controller\NeighborhoodPolygon' => 'Whathood\Controller\NeighborhoodPolygonController',
            'Whathood\Controller\Region' => 'Whathood\Controller\RegionController',
            'Whathood\Controller\RegionRest' => 'Whathood\Controller\RegionRestController',
            'Whathood\Controller\WhathoodUser' => 'Whathood\Controller\WhathoodUserController',
            'Whathood\Controller\HeatMap' => 'Whathood\Controller\HeatMapController',
            'Whathood\Controller\Search' => 'Whathood\Controller\SearchController',
            'Whathood\Controller\UserPolygon' => 'Whathood\Controller\UserPolygonController',
            'Whathood\Controller\TestPoint' => 'Whathood\Controller\TestPointController',
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

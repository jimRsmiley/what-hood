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

		/*  routes are processed in descending order, put the most important at the bottom! */

        'routes' => array(

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

            // don't put any routes before this
            'region' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/:region[/]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'Region',
                        'action'        => 'show',
                    ),
                ),
            ),

            // don't put any routes before this one either
            'neighborhood' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/:region/:neighborhood[/]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'Neighborhood',
                        'action'        => 'show',
                    ),
                ),
            ),

            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'util' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/util[/:action]',
                    'defaults' => array(
                        'controller' => 'Whathood\Controller\Util',
                        'action' => 'index'
                    )
                )
            ),

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


			/**
			 * User Polygon
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

            /**
             * User Polygon lists page
             *
             * /whathood/user-polygon/page-list/page/:page
             *
             **/
            'user_neighborhood' => array(
                'type' => 'segment',
                'may_terminate' => false,
                'options' => array(
                    'route' => '/user-neighborhood',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller' => 'Whathood\Controller\UserPolygon',
                        'action'     => 'index'
                    ),
                ),
                'child_routes' => array(
                    'user_neighborhood_page_center' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/page-center/page/:page/x/:x/y/:y[/]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Whathood\Controller',
                                'controller' => 'Whathood\Controller\UserPolygon',
                                'action'     => 'page-center',
                            ),
                        ),
                    ),
                    'user_polygon_page_list' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/page-list/page/:page',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Whathood\Controller',
                                'controller' => 'Whathood\Controller\UserPolygon',
                                'action'     => 'page-list',
                            ),
                        ),
                    ),

                    'page_neighborhood' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/page/:page/region/:region/neighborhood/:neighborhood[/]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Whathood\Controller',
                                'controller' => 'Whathood\Controller\UserPolygon',
                                'action'     => 'page-neighborhood',
                            ),
                        ),
                    ),

                    'add' => array(
                        'type'  => 'Segment',
                        'options' => array(
                            'route' => '/add',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Whathood\Controller',
                                'controller'    => 'Whathood\Controller\UserPolygon',
                                'action'        => 'add'
                            )
                        )
                    ),

                    'user_neighborhood_add_post' => array(
                        'type'  => 'Segment',
                        'options' => array(
                            'route' => '/add-post',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Whathood\Controller',
                                'controller'    => 'Whathood\Controller\UserPolygon',
                                'action'        => 'addPost'
                            )
                        )
                    ),

                    'default' => array(
                        'type'  => 'Segment',
                        'options' => array(
                            'route' => '/:action',
                            'defaults' => array(
                                'controller'    => 'Whathood\Controller\UserPolygon',
                            )
                        )
                    ),
                )
            ),

            /*
             *  /about
             */
            'about' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/about',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'Index',
                        'action'        => 'about',
                    ),
                ),
            ),

            /*
             *  /sitemap
             */
            'sitemap' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/sitemap',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Whathood\Controller',
                        'controller'    => 'Index',
                        'action'        => 'navigation',
                    ),
                ),
            ),

            /**
             *
             * REST APIs
             *
             **/
            'api_v1' => array(
                'type' => 'Segment',
                'may_terminate' => true,
                'options' => array(
                    'route' => '/api/v1',
                    'defaults' => array(
                        'controller' => 'Whathood\Controller\Activities',
                        'action' => 'index',
                    ),
                ),
                'child_routes' => array(
                    'rest_testpoint' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/testpoint',
                            'defaults' => array(
                                'controller' => 'Whathood\Controller\TestPointRestful'
                            )
                        )
                    ),

                    'rest_test_point' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/heatmap-points/neighborhood_id/:neighborhood_id',
                            'defaults' => array(
                                'controller' => 'Whathood\Controller\HeatMapRestful',
                                'action' => 'get-list'
                            )
                        )
                    ),

                    'neighborhood_border' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/neighborhood-border',
                            'defaults' => array(
                                'controller' => 'Whathood\Controller\NeighborhoodPolygonRestful',
                                'action' => 'get-list'
                            )
                        ),
                        'child_routes' => array(

                            'by_region' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/region/:region[/]',
                                    'defaults' => array(
                                        'controller' => 'Whathood\Controller\NeighborhoodPolygonRestful',
                                        'action' => 'byRegion'
                                    )
                                )
                            ),

                            'by_neighborhood_id' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:neighborhood_id[/]',
                                    'defaults' => array(
                                        'controller' => 'Whathood\Controller\NeighborhoodPolygonRestful',
                                        'action' => 'get-list'
                                    )
                                )
                            )
                        )
                    ),

                    'user_neighborhood' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/user-neighborhood',
                            'defaults' => array(
                                'controller' => 'Whathood\Controller\UserPolygonRestful',
                            )
                        ),
                        'child_routes' => array(
                            'data_tables' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/data-tables',
                                    'defaults' => array(
                                        'controller' => 'Whathood\Controller\UserPolygonRestful',
                                        'action' => 'data-tables'
                                    )
                                )
                            ),
                            'list' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/list[/page/:page][/count_per_page/:count_per_page]',
                                    'defaults' => array(
                                        'controller' => 'Whathood\Controller\UserPolygonRestful',
                                        'action' => 'list'
                                    )
                                )
                            ),
                            'default' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/:action',
                                    'defaults' => array(
                                        'controller' => 'Whathood\Controller\UserPolygonRestful',
                                    )
                                )
                            )
                        )
                    ),

                    'rest_user_polygon' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/user-polygon[/:id]',
                            'defaults' => array(
                                'controller' => 'Whathood\Controller\UserPolygonRestful',
                                'action' => 'get'
                            )
                        )
                    ),

                    'rest_whathood' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/point-election[/x/:x][/y/:y][/]',
                            'defaults' => array(
                                'controller' => 'Whathood\Controller\PointElectionRestful',
                                'action' => 'get-list'

                            )
                        )
                    ),
                )
            )
        ),
    ),


    'console' => array(
        'router' => array(
            'routes' => array(
                'np' => array(
                    'options' => array(
                        'route' => 'np',
                        'defaults' => array(
                            'controller' => 'Whathood\Controller\NeighborhoodPolygonConsole',
                            'action' => 'consoledefault'
                        )
                    )
                ),
                'up' => array(
                    'options' => array(
                        'route' => 'up',
                        'defaults' => array(
                            'controller' => 'Whathood\Controller\UserPolygonConsole',
                            'action' => 'consoledefault'
                        )
                    )
                ),
                'neighborhood' => array(
                    'options' => array(
                        'route' => 'neighborhood',
                        'defaults' => array(
                            'controller' => 'Whathood\Controller\NeighborhoodConsole',
                            'action' => 'consoledefault'
                        )
                    )
                ),
                'watcher-route' => array(
                    'options' => array(
                        'route' => 'watcher [--forever] [--force] [--neighborhood=] [--region=] [--grid-res=] [--target-precision=]',
                        'defaults' => array(
                            'controller' => 'Whathood\Controller\WatcherConsole',
                            'action' => 'watch'
                        )
                    )
                ),
                'test-point-route' => array(
                    'options' => array(
                        'route' => 'test-point show [--neighborhood=] [--region=] [--grid-res=]',
                        'defaults' => array(
                            'controller' => 'Whathood\Controller\TestPointConsole',
                            'action' => 'show'
                        )
                    )
                ),
                'neighborhood-delete' => array(
                    'options' => array(
                        'route' => 'neighborhood delete [--id=] [--neighborhood=] [--region=]',
                        'defaults' => array(
                            'controller' => 'Whathood\Controller\Neighborhood',
                            'action' => 'delete'
                        )
                    )
                ),
                'db-size-route' => array(
                    'options' => array(
                        'route' => 'postgres size',
                        'defaults' => array(
                            'controller' => 'Whathood\Controller\PostgresConsole',
                            'action' => 'show-database-size'
                        )
                    )
                ),
            )
        )
    ),

    'service_manager' => array(

        'factories' => array(

            'Whathood\Service\Caching' => function($sm) {
                // Via factory:
                $whconfig = $sm->get('Whathood\Config');
                $throw_exceptions = false;
                $cache = \Zend\Cache\StorageFactory::factory(array(
                    'adapter' => array(
                        'name'    => 'memcached',
                        'lifetime' => 7200,
                        'options' => array(
                            'ttl' => 3600,
                            'servers' => array( array('host' => "wh_memcached", 'port' => 11211 ) ),
                            'namespace'  => 'MYMEMCACHEDNAMESPACE',
                            'liboptions' => array (
                                'COMPRESSION' => true,
                                'binary_protocol' => true,
                                'no_block' => true,
                                'connect_timeout' => 100
                            )
                        ),
                    ),
                    'plugins' => array(
                        'exception_handler' => array('throw_exceptions' => $throw_exceptions)
                    ),
                ));
                return $cache;
            },

            'Whathood\ErrorHandling' =>  function($sm) {
                $logger = $sm->get('Whathood\Logger');
                try {
                    $emailer = $sm->get('Whathood\Emailer');
                }
                catch(\Exception $e) {
                    $emailer = null;
                    $logger->error("ServiceManager could not build Whathood\Emailer: ".$e->getMessage()."\n\nprevious: ".$e->getPrevious()->getMessage());
                    $logger->error("\n\n\n\n\n");
                }
                $service = new \Whathood\ErrorHandling($logger, $emailer);
                return $service;
            },

            'TimerListener' => function($sm) {
                return new \Whathood\Event\TimerListener($sm->get('Whathood\Logger'));
            },

            'Whathood\Timer' => function($sm) {
                static $timer_instance = null;
                if (null == $timer_instance) {
                    $timer_instance = new \Whathood\Timer();
                }
                return $timer_instance;
            },

            'Whathood\YamlConfig' => function($sm) {
                require_once('vendor/mustangostang/spyc/Spyc.php');
                $reader = new \Zend\Config\Reader\Yaml(array('Spyc','YAMLLoadString'));
                $global_config = $reader->fromFile('../whathood.yaml');

                if (file_exists('../whathood.local.yaml'))
                    $local_config  = $reader->fromFile('../whathood.local.yaml');
                else
                    $local_config = array();
                return array_replace_recursive($global_config, $local_config);
            },

            'Whathood\Config' => function($sm) {
                $yaml_config = $sm->get('Whathood\YamlConfig');
                return new \Zend\Config\Config($yaml_config);
            },

            'Whathood\Logger' => function($sm) {
                $config = $sm->get('Whathood\Config');

                $file_writer = new \Zend\Log\Writer\Stream($config->log_file);

                $filter = new \Zend\Log\Filter\Priority(Logger::INFO);
                $file_writer->addFilter($filter);
                $logger = new \Whathood\Logger;
                $logger->addWriter($file_writer);

                // DO NOT THINK THIS IS WORKING register the logger to handle php errors
                #\Zend\Log\Logger::registerErrorHandler($logger);
                #\Zend\Log\Logger::registerExceptionHandler($logger);

                return $logger;
            },

            /*
             * get the regular file logger and add the console writer
             * to it
             */
            'Whathood\ConsoleLogger' => function($sm) {
                $logger = $sm->get('Whathood\Logger');
                $console_writer = new \Zend\Log\Writer\Stream('php://output');

                $filter = new \Zend\Log\Filter\Priority(Logger::INFO);
                $console_writer->addFilter($filter);

                $logger->addWriter($console_writer);
                return $logger;
            },

            'Whathood\Emailer' => function($sm) {
                $config = $sm->get('Whathood\Config');
                $emailer = \Whathood\Email::build(array_merge($config['email']->toArray(), array('logger' => $sm->get('Whathood\Logger'))));
                return $emailer;
            },

            'Whathood\Service\Messaging' => function($sm) {
                $emailer = $sm->get("Whathood\Emailer");
                $messenger = \Whathood\Service\MessagingService::build(array('emailer'=>$emailer));
                return $messenger;
            },

            'mydoctrineentitymanager'  => function($sm) {
                $em = $sm->get('doctrine.entitymanager.orm_default');
                return $em;
            },

            'Whathood\SchemaTool'  => function($sm) {
                return new \Whathood\SchemaTool($sm);
            },

            'Whathood\Mapper\PointsAsPolygonMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Whathood\Mapper\PointsAsPolygonMapper( $sm, $em );
                return $mapper;
            },

            'Whathood\Mapper\HeatMapPoint'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Whathood\Mapper\HeatMapPoint( $sm, $em );
                return $mapper;
            },

            'Whathood\Mapper\PostgresMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Whathood\Mapper\PostgresMapper( $sm, $em );
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

            'Whathood\Mapper\PointElectionMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                return new \Whathood\Mapper\PointElectionMapper($sm,$em);
            },

            'Whathood\Mapper\UserPolygonMapper'  => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                return new \Whathood\Mapper\UserPolygonMapper($sm,$em);
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
                $cacher = $sm->get('Whathood\Service\Caching');
                $mapper = new \Whathood\Mapper\NeighborhoodPolygonMapper( $sm, $em, $cacher );
                return $mapper;
            },

            'Whathood\Mapper\Builder' => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Whathood\Mapper\Builder( $sm, $em );
                return $mapper;
            },

            'Whathood\Mapper\TestPointMapper' => function($sm) {
                $em = $sm->get('mydoctrineentitymanager');
                $mapper = new \Whathood\Mapper\TestPointMapper( $sm, $em );
                return $mapper;
            },
        ),
    ),

    'controllers' => array(
        'invokables' => array(

            /* mvc controllers */
            'Whathood\Controller\Admin' => 'Whathood\Controller\AdminController',
            'Whathood\Controller\Util' => 'Whathood\Controller\UtilController',
            'Whathood\Controller\ContentiousPoint' => 'Whathood\Controller\ContentiousPointController',
            'Whathood\Controller\CreateEvent' => 'Whathood\Controller\CreateEventController',
            'Whathood\Controller\Index' => 'Whathood\Controller\IndexController',
            'Whathood\Controller\Neighborhood' => 'Whathood\Controller\NeighborhoodController',
            'Whathood\Controller\NeighborhoodPolygon' => 'Whathood\Controller\NeighborhoodPolygonController',
            'Whathood\Controller\Region' => 'Whathood\Controller\RegionController',
            'Whathood\Controller\WhathoodUser' => 'Whathood\Controller\WhathoodUserController',
            'Whathood\Controller\HeatMap' => 'Whathood\Controller\HeatMapController',
            'Whathood\Controller\Search' => 'Whathood\Controller\SearchController',
            'Whathood\Controller\UserPolygon' => 'Whathood\Controller\UserPolygonController',
            'Whathood\Controller\TestPoint' => 'Whathood\Controller\TestPointController',

            /* restful controllers */
            'Whathood\Controller\NeighborhoodPolygonRestful'    => 'Whathood\Controller\Restful\NeighborhoodPolygonRestfulController',
            'Whathood\Controller\UserPolygonRestful'            => 'Whathood\Controller\Restful\UserPolygonController',
            'Whathood\Controller\RegionRestful'                 => 'Whathood\Controller\RegionRestController',
            'Whathood\Controller\PointElectionRestful'          => 'Whathood\Controller\Restful\PointElectionController',
            'Whathood\Controller\TestPointRestful'              => 'Whathood\Controller\Restful\TestPointRestfulController',
            'Whathood\Controller\HeatMapRestful'                => 'Whathood\Controller\Restful\HeatMapController',

            /* console controllers */
            'Whathood\Controller\PostgresConsole'               => 'Whathood\Controller\Console\PostgresController',
            'Whathood\Controller\WatcherConsole'                => 'Whathood\Controller\Console\WatcherController',
            'Whathood\Controller\TestPointConsole'              => 'Whathood\Controller\Console\TestPointController',
            'Whathood\Controller\NeighborhoodConsole'           => 'Whathood\Controller\Console\NeighborhoodController',
            'Whathood\Controller\UserPolygonConsole'            => 'Whathood\Controller\Console\UserPolygonController',
            'Whathood\Controller\NeighborhoodPolygonConsole'    => 'Whathood\Controller\Console\NeighborhoodPolygonController',
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

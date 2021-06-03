<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/*
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 */
/** @var RouteBuilder $routes */
$routes->setRouteClass(DashedRoute::class);
$routes->scope('/', function (RouteBuilder $builder) {
    $builder->setExtensions(['json', 'xml']);
    /**
     * this fake route is necessary to prevent Cakephp from complaint about missing route for '/'
     * It will never be called cause Middleware will intercept and redirect the request
     */
    $builder->connect('/', ['controller' => 'Fake']);
    $builder->redirect(
        '/',
        ['controller' => 'Pages', 'action' => 'execRedirect'],
        ['routeClass' => 'ADmad/I18n.I18nRoute']
    // Or ['persist'=>['id']] for default routing where the
    // view action expects $id as an argument.
    );
    /**
     * once middleware will intercept route for '/' the following i18n route will be used!
     */

    /*$builder->connect(
        '/',
        ['controller' => 'journeys', 'action' => 'search'],
        ['routeClass' => 'ADmad/I18n.I18nRoute']
    );

    $builder->connect(
        '/journeys/search',
        ['controller' => 'journeys', 'action' => 'search'],
        ['routeClass' => 'ADmad/I18n.I18nRoute']
    );

    $builder->connect(
        '/:controller/:action',
        [],
        ['routeClass' => 'ADmad/I18n.I18nRoute']
    );*/

    /*
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    //$builder->connect('/pages/*', 'Pages::display');

    /*
     * Connect catchall routes for all controllers.
     *
     * The `fallbacks` method is a shortcut for
     *
     * ```
     * $builder->connect('/:controller', ['action' => 'index']);
     * $builder->connect('/:controller/:action/*', []);
     * ```
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $builder->fallbacks('ADmad/I18n.I18nRoute');
});

/*
 * If you need a different set of middleware or none at all,
 * open new scope and define routes there.
 *
 * ```
 * $routes->scope('/api', function (RouteBuilder $builder) {
 *     // No $builder->applyMiddleware() here.
 *
 *     // Parse specified extensions from URLs
 *     // $builder->setExtensions(['json', 'xml']);
 *
 *     // Connect API actions here.
 * });
 * ```
 */

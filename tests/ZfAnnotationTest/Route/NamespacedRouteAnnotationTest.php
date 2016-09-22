<?php

namespace ZfAnnotationTest\Route;

use ZfAnnotation\EventListener\RouteListener;
use ZfAnnotationTest\AnnotationTestCase;

/**
 * @group zfa-router
 * @group zfa-router-namespaced
 */
class NamespacedRouteAnnotationTest extends AnnotationTestCase
{

    protected function setUp()
    {
        $this->listener = new RouteListener;
    }

    public function testChildNodesAdded()
    {
        $config = $this->parse(TestAsset\NamespacedController::class)['router']['routes'];

        $expected = array(
            'root' => array(
                'type' => 'segment',
                'priority' => 0,
                'options' => array(
                    'route' => '/root/:id/:method',
                    'defaults' => array(
                        'controller' => 'namespaced',
                        'action' => 'index'
                    ),
                    'constraints' => array(
                        'id' => '\\d+',
                        'method' => '\\w+'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'index' => array(
                        'type' => 'segment',
                        'priority' => 0,
                        'options' => array(
                            'route' => '/root/:id/:method',
                            'defaults' => array(
                                'controller' => 'nobase',
                                'action' => 'complete-definition-action'
                            ),
                            'constraints' => array(
                                'id' => '\\d+',
                                'method' => '\\w+'
                            )
                        ),
                        'may_terminate' => true,
                        'child_routes' => array()
                    ),
                    'edit' => array(
                        'type' => 'literal',
                        'priority' => 0,
                        'options' => array(
                            'route' => '/edit',
                            'defaults' => array(
                                'controller' => TestAsset\NamespacedController::class,
                                'action' => 'edit'
                            ),
                            'constraints' => null
                        ),
                        'child_routes' => array(),
                        'may_terminate' => true
                    ),
                    'remove' => array(
                        'type' => 'literal',
                        'priority' => 0,
                        'options' => array(
                            'route' => '/remove',
                            'defaults' => array(
                                'controller' => TestAsset\NamespacedController::class,
                                'action' => 'remove'
                            ),
                            'constraints' => null
                        ),
                        'child_routes' => array(),
                        'may_terminate' => true
                    )
                )
            )
        );

        $this->assertEquals($expected, $config);
    }

}

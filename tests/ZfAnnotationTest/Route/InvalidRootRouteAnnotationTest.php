<?php

namespace ZfAnnotationTest\Route;

use ZfAnnotation\EventListener\RouteListener;
use ZfAnnotationTest\AnnotationTestCase;

/**
 * @group zfa-router
 * @group zfa-router-invalid-root-node
 */
class InvalidRootRouteAnnotationTest extends AnnotationTestCase
{

    protected function setUp()
    {
        $this->listener = new RouteListener;
    }

    /**
     * @expectedException ZfAnnotation\Exception\InvalidArgumentException
     */
    public function testExceptionThrown()
    {
        $this->parse(TestAsset\InvalidRootRouteController::class);
    }

}

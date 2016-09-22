<?php

use ZfAnnotation\EventListener\RouteListener;
use ZfAnnotation\EventListener\ServiceListener;
use ZfAnnotation\Factory\AnnotationReaderFactory;
use ZfAnnotation\Service\ClassParserFactory;

/**
 * Annotated Router module for Zend Framework 2
 *
 * @link      https://github.com/alex-oleshkevich/zf2-annotated-routerfor the canonical source repository.
 * @copyright Copyright (c) 2014-2016 Alex Oleshkevich <alex.oleshkevich@gmail.com>
 * @license   http://en.wikipedia.org/wiki/MIT_License MIT
 */
return [
    'zf_annotation' => [
        'scan_modules' => [],
        'namespaces' => [
            'ZfAnnotation\Annotation'
        ],
        'annotations' => [],
        'event_listeners' => [
            RouteListener::class,
            ServiceListener::class
        ],
        'cache' => sys_get_temp_dir() . '/zfa-cache',
        'cache_debug' => false
    ],
    'service_manager' => [
        'factories' => [
            'ZfAnnotation\AnnotationReader' => AnnotationReaderFactory::class,
            'ZfAnnotation\Parser' => ClassParserFactory::class,
        ]
    ],
];

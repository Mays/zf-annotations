<?php

/**
 * Annotation module for Zend Framework 2.
 *
 * @link      https://github.com/alex-oleshkevich/zf-annotations the canonical source repository.
 * @copyright Copyright (c) 2014-2016 Alex Oleshkevich <alex.oleshkevich@gmail.com>
 * @license   http://en.wikipedia.org/wiki/MIT_License MIT
 */

namespace ZfAnnotation\EventListener;

use ReflectionClass;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZfAnnotation\Annotation\Service;
use ZfAnnotation\Event\ParseEvent;
use ZfAnnotation\Exception\InvalidAnnotationException;

/**
 * Collects service manager annotations.
 */
class ServiceListener extends AbstractListenerAggregate
{

    /**
     * @var array
     */
    protected $definitions = [];

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(ParseEvent::EVENT_CLASS_PARSED, [$this, 'onClassParsed']);
    }

    /**
     * @param ParseEvent $event
     */
    public function onClassParsed(ParseEvent $event)
    {
        $classHolder = $event->getTarget();
        $classAnnotations = $classHolder->getAnnotations();
        foreach ($classAnnotations as $annotation) {
            if (!$annotation instanceof Service) {
                continue;
            }

            $this->handleClassAnnotation($annotation, $classHolder->getClass());
        }
        $event->mergeResult($this->definitions);
    }

    public function handleClassAnnotation(Service $annotation, ReflectionClass $class)
    {
        if (!$annotation->getName()) {
            $annotation->setName($class->getName());
        }
        
        if ($annotation->getType() == 'invokable' && $annotation->getFactoryClass()) {
            $annotation->setType('factory');
        }

        switch ($annotation->getType()) {
            case 'invokable':
                $this->definitions[$annotation->getServiceManager()]['invokables'][$annotation->getName()] = $class->getName();
                break;
            case 'factory':
                if (!empty($annotation->getFactoryClass())) {
                    $this->definitions[$annotation->getServiceManager()]['factories'][$annotation->getName()] = $annotation->getFactoryClass();
                } else {
                    if (!in_array(FactoryInterface::class, $class->getInterfaceNames())) {
                        throw new InvalidAnnotationException('Service factory class must implement "' . FactoryInterface::class . '".');
                    }
                    $this->definitions[$annotation->getServiceManager()]['factories'][$annotation->getName()] = $class->getName();
                }
                break;
            case 'abstractFactory':
                if (!in_array(AbstractFactoryInterface::class, $class->getInterfaceNames())) {
                    throw new InvalidAnnotationException('Abstract service factory class must implement "' . AbstractFactoryInterface::class . '".');
                }
                $this->definitions[$annotation->getServiceManager()]['abstract_factories'][] = $class->getName();
                break;
            case 'delegator':
                if (!in_array(DelegatorFactoryInterface::class, $class->getInterfaceNames())) {
                    throw new InvalidAnnotationException('Delegator must implement "' . DelegatorFactoryInterface::class . '".');
                }
                if (empty($annotation->getFor())) {
                    throw new InvalidAnnotationException('Delegator annotation must contain "for" option.');
                }
                if (!isset($this->definitions[$annotation->getServiceManager()]['delegators'][$annotation->getFor()])) {
                    $this->definitions[$annotation->getServiceManager()]['delegators'][$annotation->getFor()] = [];
                }
                $this->definitions[$annotation->getServiceManager()]['delegators'][$annotation->getFor()][] = $class->getName();
                break;
            default:
                throw new InvalidAnnotationException('Service annotation must have "type" property value. Seen in ' . $class->getName());
        }

        $allowedToShareAndAlias = ['invokable', 'factory'];
        if (in_array($annotation->getType(), $allowedToShareAndAlias)) {
            if (is_bool($annotation->getShared())) {
                $this->definitions[$annotation->getServiceManager()]['shared'][$annotation->getName()] = $annotation->getShared();
            }

            foreach ($annotation->getAliases() as $alias) {
                $this->definitions[$annotation->getServiceManager()]['aliases'][$alias] = $annotation->getName();
            }
        }
    }

}

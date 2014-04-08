<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\toolkit\func;

use umi\event\IEventObservant;
use umi\event\TEventObservant;
use umi\toolkit\IToolkit;
use umi\toolkit\Toolkit;
use utest\TestCase;
use utest\toolkit\mock\MockTools;
use utest\toolkit\mock\TestFactory;
use utest\toolkit\mock\TestObject;

/**
 * Тест событий возникающих при создании объектов через фабрики
 */
class EventsTest extends TestCase implements IEventObservant
{
    use TEventObservant;

    /**
     * @var IToolkit $toolkit
     */
    protected $toolkit;
    /**
     * @var bool $eventCaught признак, что событие было обработано
     */
    protected $eventCaught = false;

    protected function setUpFixtures()
    {
        $this->toolkit = new Toolkit();
        $this->toolkit->registerToolbox(
            [
                'name'              => MockTools::NAME,
                'class'             => 'utest\toolkit\mock\MockTools',
                'services'          => [
                    'utest\toolkit\mock\TestFactory'
                ]
            ]
        );

        $this->subscribeTo($this->toolkit);
        $this->bindEvent(TestObject::EVENT_TEST, function() {
                $this->eventCaught = true;
            }
        );

    }

    public function testEventCaught()
    {
        /**
         * @var TestFactory $factory
         */
        $factory = $this->toolkit->getService('utest\toolkit\mock\TestFactory');
        $object = $factory->createObject();

        $object->makeEvent();
        $this->assertTrue(
            $this->eventCaught,
            'Ожидается, что чобытие, вызванное объектом созданным через прототип, поднялось до тулкита'
        );

    }
}
 
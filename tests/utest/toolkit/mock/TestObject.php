<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\toolkit\mock;

use umi\event\IEventObservant;
use umi\event\TEventObservant;

/**
 * Объект, поднимающий событие
 */
class TestObject implements IEventObservant
{
    use TEventObservant;

    const EVENT_TEST = 'umi:event:test';

    public function makeEvent()
    {
        $this->fireEvent(self::EVENT_TEST);
    }

}
 
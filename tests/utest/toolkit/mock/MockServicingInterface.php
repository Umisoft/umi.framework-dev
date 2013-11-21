<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\toolkit\mock;

/**
 * Интерфейс для поддержки тестового тулбокса
 */
interface MockServicingInterface
{
    /**
     * Метод, через который тулбоксом внедряется зависимость
     * @param $value
     * @return self
     */
    public function setDependency($value);

    /**
     * @param $value
     * @return self
     */
    public function setService($value);

    /**
     * Метод, используемый инициализатором прототипа
     * @param $value
     * @return self
     */
    public function setInitializerService($value);
}

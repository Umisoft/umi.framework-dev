<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata\field\special;

use umi\orm\metadata\field\IField;
use umi\orm\metadata\field\special\DelayedField;
use utest\orm\unit\metadata\field\FieldTestCase;

/**
 * Тест поля хранителя связи.
 */
class DelayedFieldTest extends FieldTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getField()
    {
        return new DelayedField(
            'mock',
            IField::TYPE_DELAYED,
            [
                'dataType' => 'string',
                'formula' => 'recalculateValue'
            ]
        );
    }

    public function testConfig()
    {
        $config = [];
        $e = null;
        try {
            new DelayedField('mock', IField::TYPE_DELAYED, $config);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать поле DelayedField без указания типа данных'
        );

        $config['dataType'] = 'wrongDataType';
        $e = null;
        try {
            new DelayedField('mock', IField::TYPE_DELAYED, $config);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\OutOfBoundsException',
            $e,
            'Ожидается исключение при попытке создать поле DelayedField с неверным типом данных'
        );

        $config['dataType'] = 'string';
        $e = null;
        try {
            new DelayedField('mock', IField::TYPE_DELAYED, $config);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать поле DelayedField без указания на метод, вычисляющий значение'
        );

    }

}

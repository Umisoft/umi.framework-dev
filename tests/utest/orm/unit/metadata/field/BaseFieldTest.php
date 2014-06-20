<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata\field;

use Doctrine\DBAL\Types\Type;
use umi\orm\metadata\field\BaseField;
use umi\orm\metadata\field\IField;
use umi\orm\object\IObject;

/**
 * Тест методов базового класса BaseField.
 */
class BaseFieldTest extends FieldTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getField()
    {
        return new MockField('mock', IField::TYPE_STRING);
    }

    public function testDefaultConfig()
    {

        $field = new MockField('mock', IField::TYPE_STRING);

        $this->assertEquals('mock', $field->getName(), 'Неверное имя поля');
        $this->assertEquals(
            $field->getName(),
            $field->getColumnName(),
            'Ожидается, что по умолчанию имя колонки совпадает с именем поля'
        );

        $this->assertFalse($field->getIsReadOnly(), 'Ожидается, что по умолчанию поле доступно на изменение');
        $this->assertNull($field->getDefaultValue(), 'Ожидается, что по умолчанию дефолное значение у поля null');
        $this->assertNull($field->getAccessor(), 'Ожидается, что по умолчанию метод доступа к значению не установлен');
        $this->assertNull($field->getMutator(), 'Ожидается, что по умолчанию метод изменения значения не установлен');
        $this->assertEmpty($field->getValidatorsConfig(), 'Ожидается, что по умолчанию валидаторы для поля не установлены');
        $this->assertEmpty($field->getFiltersConfig(), 'Ожидается, что по умолчанию фильтры для поля не установлены');
        $this->assertFalse(
            $field->getIsLocalized(),
            'Ожидается, что поле не локализовано, если у него не указаны локали'
        );
        $this->assertEmpty(
            $field->getLocalizations(),
            'Если в конфиге локали не указаны, ожидается, что никакие локали не будут возвращены'
        );

    }

    public function testConfig()
    {

        $field = new MockField(
            'mock',
            IField::TYPE_STRING,
            [
                'columnName' => 'column_for_field',
                'locked' => 1,
                'readOnly' => 1,
                'defaultValue' => 10,
                'accessor' => 'getField',
                'mutator' => 'setField',
                'validators' => [
                    'fieldValidator' => [
                        'from' => 5,
                        'to'   => 15
                    ]
                ],
                'filters' => [
                    'fieldFilter' => [
                        'from' => 5,
                        'to'   => 15
                    ]
                ]
            ]
        );

        $this->assertEquals('column_for_field', $field->getColumnName(), 'Неверно определено имя колонки бд для поля');
        $this->assertTrue($field->getIsReadOnly(), 'Неверно прочитана возможность редактировать поле');
        $this->assertSame(10, $field->getDefaultValue(), 'Неверно прочитано дефолтное значение поля');
        $this->assertEquals('getField', $field->getAccessor(), 'Неверно прочитан метод доступа к значению поля');
        $this->assertEquals('setField', $field->getMutator(), 'Неверно прочитан метод модификации значения поля');
        $this->assertEquals(
            ['fieldValidator' => ['from' => 5, 'to' => 15]],
            $field->getValidatorsConfig(),
            'Неверно прочитаны валидаторы поля'
        );
        $this->assertEquals(
            ['fieldFilter' => ['from' => 5, 'to' => 15]],
            $field->getFiltersConfig(),
            'Неверно прочитаны фильтры поля'
        );
    }

    public function testLocalesConfig()
    {
        $localizations = [
            'ru' => [
                'columnName'   => 'field_ru',
                'defaultValue' => 'default_ru',
                'validators' => ['ruFieldValidator' => []],
                'filters' => ['ruFieldFilter' => []],
            ],
            'en' => [
                'columnName'   => 'field_en',
                'defaultValue' => 'default_en',
                'validators' => ['enFieldValidator' => []],
                'filters' => ['enFieldFilter' => []],
            ]
        ];

        $field = new MockField(
            'mock',
            IField::TYPE_STRING,
            [
                'columnName'    => 'field_ru',
                'defaultValue'  => 'default_ru',
                'validators' => ['ruFieldValidator' => []],
                'filters' => ['ruFieldFilter' => []],
                'localizations' => $localizations
            ]
        );
        $this->assertTrue(
            $field->getIsLocalized(),
            'Ожидается, что локализуемое поле локализовано, когда у него есть список локалей'
        );
        $this->assertEquals($localizations, $field->getLocalizations(), 'Неверно прочитан конфиг локализаций');
        $this->assertTrue($field->hasLocale('ru'), 'Ожидается, что локаль ru есть у поля');
        $this->assertFalse($field->hasLocale('de'), 'Ожидается, что локаль de отсутствует у поля');

        $this->assertEquals(
            'field_ru',
            $field->getColumnName(),
            'Ожидается, что при запросе локализованного столбца без указания локали вернется столбец по умолчанию'
        );
        $this->assertEquals(
            'default_ru',
            $field->getDefaultValue(),
            'Ожидается, что при запросе локализованного дефолтного значения без указания локали '
            . 'вернется значение по умолчанию'
        );
        $this->assertEquals(
            ['ruFieldValidator' => []],
            $field->getValidatorsConfig(),
            'Ожидается, что при запросе локализованного конфига валидаторов без указания локали '
            . 'вернется значение по умолчанию'
        );
        $this->assertEquals(
            ['ruFieldFilter' => []],
            $field->getFiltersConfig(),
            'Ожидается, что при запросе локализованного конфига фильтров без указания локали '
            . 'вернется значение по умолчанию'
        );

        $this->assertEquals(
            'field_en',
            $field->getColumnName('en'),
            'Ожидается, что при запросе локализованного столбца вернется столбец для указанной локали'
        );
        $this->assertEquals(
            'default_en',
            $field->getDefaultValue('en'),
            'Ожидается, что при запросе локализованного дефолтного значения вернется значение для указанной локали'
        );
        $this->assertEquals(
            ['enFieldValidator' => []],
            $field->getValidatorsConfig('en'),
            'Ожидается, что при запросе локализованного конфига валидаторов вернется конфиг для указанной локали'
        );
        $this->assertEquals(
            ['enFieldFilter' => []],
            $field->getFiltersConfig('en'),
            'Ожидается, что при запросе локализованного конфига фильтров вернется конфиг для указанной локали'
        );

        $e = null;
        try {
            $field->getColumnName('it');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить имя колонки для несуществующей локали'
        );

        $e = null;
        try {
            $field->getDefaultValue('it');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить значение по умолчанию для несуществующей локали'
        );

        $e = null;
        try {
            $field->getValidatorsConfig('it');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить конфиг валидаторов для несуществующей локали'
        );

        $e = null;
        try {
            $field->getFiltersConfig('it');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить конфиг фильтров для несуществующей локали'
        );
    }

    public function testWrongValidatorsConfig()
    {
        $e = null;
        try {
            new MockField('mock', IField::TYPE_STRING, ['validators' => 'WrongValidatorsConfig']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке выставить неверную конфигурацию валидаторов'
        );
    }

    public function testWrongFiltersConfig()
    {
        $e = null;
        try {
            new MockField('mock', IField::TYPE_STRING, ['filters' => 'WrongFiltersConfig']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке выставить неверную конфигурацию фильтров'
        );
    }

    public function testWrongLocalesConfig()
    {
        $e = null;
        try {
            new MockField('mock', IField::TYPE_STRING, ['localizations' => 'wrongLocalizationsConfig']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать поле с неверной конфигурацией локалей'
        );
    }
}

class MockField extends BaseField
{
    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return Type::STRING;
    }

    /**
     * {@inheritdoc}
     */
    public function validateInputPropertyValue($propertyValue)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function preparePropertyValue(IObject $object, $internalDbValue)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDbValue(IObject $object, $propertyValue)
    {
        return null;
    }
}

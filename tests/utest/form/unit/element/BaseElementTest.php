<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\element;

use umi\filter\IFilterFactory;
use umi\filter\toolbox\factory\FilterFactory;
use umi\form\adapter\DefaultFormAdapter;
use umi\form\element\IFormElement;
use umi\form\Form;
use umi\validation\IValidatorFactory;
use umi\validation\toolbox\factory\ValidatorFactory;
use utest\form\FormTestCase;

/**
 * Базовые тесты элементов.
 */
abstract class BaseElementTest extends FormTestCase
{
    /**
     * Создает элемент с заданными параметрами
     * @param string $name имя элемента
     * @param array $attributes аттрибуты
     * @param array $options опции
     * @return IFormElement элемент
     */
    abstract public function getFormElement($name, array $attributes = [], array $options = []);

    /**
     * Базовые тесты.
     */
    public function testBasic()
    {
        $form = new Form('testForm');
        $element = $this->getFormElement('testElement', ['data-id' => 'id']);
        $element->setParent($form);

        $this->assertArrayHasKey(
            'data-id',
            $element->getAttributes(),
            'Ожидается, что аттрибуты будут установлены.'
        );

        $this->assertEquals('testElement', $element->getName(), 'Ожидается, что имя элемента будет установлено.');

        $this->assertEquals('testElement', $element->getName());
    }

    /**
     * Тест установки/получения значений элементов.
     */
    public function testValues()
    {
        $form = new Form('testForm');
        $element = $this->getFormElement(
            'testElement',
            ['data-id' => 'id'],
            ['default' => 'test value', 'label' => 'My element']
        );
        $element->setParent($form);

        $this->assertSame($element, $element->setValue('New value'));
        $this->assertEquals('New value', $element->getValue(), 'Ожидается, что значение будет установлено.');
    }

    /**
     * Фильтров входных данных.
     */
    public function testValidators()
    {
        $form = new Form('testForm');
        $form->setDataAdapter(new DefaultFormAdapter());

        $e = $this->getFormElement('test');
        $form->add($e);

        $this->assertInstanceOf(
            'umi\validation\IValidatorCollection',
            $e->getValidators(),
            'Ожидается, что цепочку валидаторов можно получить у любого элемента.'
        );

        $e->getValidators()->appendValidator($this->getValidator(IValidatorFactory::TYPE_REQUIRED));

        $this->assertTrue($e->isValid());
        $form->setData(['test' => '']);
        $this->assertFalse($e->isValid());
    }

    /**
     * Фильтров входных данных.
     */
    public function testFilters()
    {
        $form = new Form('testForm');
        $e = $this->getFormElement('test');
        $form->add($e);

        $this->assertInstanceOf(
            'umi\filter\IFilterCollection',
            $e->getFilters(),
            'Ожидается, что цепочку фильтров можно получить у любого элемента.'
        );

        $e->getFilters()->appendFilter($this->getFilter(IFilterFactory::TYPE_INT));

        $form->setData(['test' => '1aa']);

        $this->assertEquals(1, $e->getValue());
    }


    protected function getValidator($type, array $options = [])
    {
        $validatorFactory = new ValidatorFactory();
        $this->resolveOptionalDependencies($validatorFactory);

        return $validatorFactory->createValidator($type, $options);
    }


    protected function getFilter($type, array $options = [])
    {
        $filterFactory = new FilterFactory();
        $this->resolveOptionalDependencies($filterFactory);

        return $filterFactory->createFilter($type, $options);
    }

}
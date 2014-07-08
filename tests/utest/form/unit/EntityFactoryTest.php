<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit;

use umi\form\exception\OutOfBoundsException;
use umi\form\toolbox\factory\EntityFactory;
use umi\toolkit\factory\TFactory;
use utest\form\FormTestCase;

/**
 * Тесты фабрики элементов формы.
 */
class EntityFactoryTest extends FormTestCase
{
    /**
     * @var EntityFactory $factory фабрика элементов формы.
     */
    public $factory;

    public function setUpFixtures()
    {
        $this->factory = new EntityFactory();
        $this->resolveOptionalDependencies($this->factory);
    }

    /**
     * Тест создания элемента формы.
     */
    public function testCreateEntity()
    {
        $this->assertInstanceOf(
            $this->factory->elementTypes['text'],
            $this->factory->createFormEntity('test', ['type' => 'text']),
            'Ожидается, что будет создан текстовый элемент.'
        );

        $this->assertInstanceOf(
            $this->factory->fieldSetTypes['fieldset'],
            $this->factory->createFormEntity('test', ['type' => 'fieldset', 'elements' => ['test' => ['type' => 'text']]]),
            'Ожидается, что будет создана группа полей.'
        );
    }

    /**
     * @test исключение, если тип элемента не известен.
     * @expectedException OutOfBoundsException
     */
    public function invalidElementType()
    {
        $this->factory->createFormEntity('test', ['type' => 'NaN']);
    }


    /**
     * Тест создания формы.
     */
    public function testFormCreation()
    {
        $form = $this->factory
            ->createForm(
                [
                    'action' => '/',
                    'elements' => [
                        'test' => ['type' => 'text']
                    ]
                ]
            );

        $this->assertInstanceOf('umi\form\Form', $form, 'Ожидается, что форма будет создана.');
        $this->assertInstanceOf(
            'umi\form\element\Text',
            $form->get('test'),
            'Ожидается, что форма будет содержать элемент.'
        );
    }
}
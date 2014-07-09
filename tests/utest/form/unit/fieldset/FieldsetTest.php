<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\fieldset;

use umi\form\element\Text;
use umi\form\fieldset\FieldSet;
use umi\form\Form;
use utest\form\FormTestCase;

/**
 * Тесты группы полей формы.
 */
class FieldsetTest extends FormTestCase
{
    /**
     * @var FieldSet $fieldset группа полей
     */
    public $fieldset;

    public function setUpFixtures()
    {
        $form = new Form('testForm', ['action' => '/', 'method' => 'GET']);

        $text1 = new Text('element1');
        $this->resolveOptionalDependencies($text1);
        $text2 = new Text('element2');
        $this->resolveOptionalDependencies($text2);
        $text3 = new Text('element3');
        $this->resolveOptionalDependencies($text3);

        $this->fieldset = new FieldSet('test');
        $this->fieldset->add($text1);
        $this->fieldset->add($text2);
        $this->fieldset->add($text3);

        $form->add($this->fieldset);

    }

    /**
     * Тест получения элемента.
     */
    public function testGetElement()
    {
        $el = $this->fieldset->get('element1');
        $this->assertInstanceOf('umi\form\element\Text', $el, 'Ожидается, что будет получен элемент.');
        $this->assertEquals('element1', $el->getName(), 'Ожидается, что будет получен элемент с заданным имененем.');
    }

    /**
     * @test исключения, при попытке получить несуществующий элемент.
     * @expectedException \umi\form\exception\OutOfBoundsException
     */
    public function notExistingElement()
    {
        $this->fieldset->get('element10');
    }

    /**
     * Тест обходимости группы полей.
     */
    public function testTraversable()
    {
        $elements = iterator_to_array($this->fieldset);
        $this->assertEquals(
            [
                'element1',
                'element2',
                'element3',
            ],
            array_keys($elements),
            'Ожидается, что группа полей может быть использована в foreach.'
        );
    }

    /**
     * Тест установки и проверки данных в группу полей.
     */
    public function testData()
    {
        $this->assertEquals(
            [
                'element1' => null,
                'element2' => null,
                'element3' => null
            ],
            $this->fieldset->getData(),
            'Ожидается, что данные не были установлены.'
        );

        $this->assertSame(
            $this->fieldset,
            $this->fieldset->setData(
                [
                    'element1' => 'test value 1',
                    'element2' => 'test value 2'
                ]
            ),
            'Ожидается, что будет возвращен $this'
        );

        $this->assertTrue($this->fieldset->isValid(), 'Ожидается, что валидация прошла успешно.');

        $this->assertEquals(
            [
                'element1' => 'test value 1',
                'element2' => 'test value 2',
                'element3' => null,
            ],
            $this->fieldset->getData(),
            'Ожидается, что данные будут установлены и получены.'
        );
    }

    /**
     * Тест группы полей с группой полей. (вложенные группы полей)
     */
    public function testFieldsetOfFieldset()
    {
        $fieldset = new FieldSet('test');
        $text4 = new Text('element4');
        $this->resolveOptionalDependencies($text4);

        $fieldset->add($text4);
        $this->fieldset->add($fieldset);

        $this->fieldset->setData(
            [
                'element1' => 'test value 1',
                'element2' => 'test value 2',
                'element3' => 'test value 3',
                'test' => [
                    'element4' => 'test value 4'
                ]
            ]
        );

        $this->assertTrue($this->fieldset->isValid(), 'Ожидается, что валидация прошла успешно.');

        $this->assertEquals(
            [
                'element1' => 'test value 1',
                'element2' => 'test value 2',
                'element3' => 'test value 3',
                'test' => [
                    'element4' => 'test value 4'
                ]
            ],
            $this->fieldset->getData(),
            'Ожидается, что данные будут установлены и получены.'
        );
    }
}
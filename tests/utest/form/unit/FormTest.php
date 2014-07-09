<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit;

use umi\form\element\Text;
use umi\form\exception\OutOfBoundsException;
use umi\form\Form;
use utest\form\FormTestCase;

/**
 * Тесты формы.
 */
class FormTest extends FormTestCase
{
    /**
     * @var Form $form форма
     */
    public $form;

    public function setUpFixtures()
    {
        $this->form = new Form('testForm', ['action' => '/', 'method' => 'GET']);
        $this->form->add(new Text('element1'));
        $this->form->add(new Text('element2'));
        $this->form->add(new Text('element3'));

        $this->resolveOptionalDependencies($this->form);
    }

    /**
     * Тест получения элемента из формы.
     */
    public function testGetElement()
    {
        $el = $this->form->get('element1');
        $this->assertInstanceOf('umi\form\element\Text', $el, 'Ожидается, что будет получен элемент.');
        $this->assertEquals('element1', $el->getName(), 'Ожидается, что будет получен элемент с заданным имененем.');
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     */
    public function notExistingElement()
    {
        $this->form->get('element10');
    }

    /**
     * Тест аттрибутов формы.
     */
    public function testForm()
    {
        $this->assertEquals('/', $this->form->getAction(), 'Ожидается, что action установлен.');
        $this->assertEquals('GET', $this->form->getMethod(), 'Ожидается, что метод установлен в GET.');
    }

    /**
     * Тест обходимости формы.
     */
    public function testTraversable()
    {
        $elements = iterator_to_array($this->form);
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
}
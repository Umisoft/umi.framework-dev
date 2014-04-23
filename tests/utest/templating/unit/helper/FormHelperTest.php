<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\helper;

use umi\form\element\Button;
use umi\form\element\Checkbox;
use umi\form\element\Hidden;
use umi\form\element\Select;
use umi\form\element\Textarea;
use umi\form\IForm;
use umi\form\toolbox\factory\EntityFactory;
use umi\templating\helper\form\FormHelper;
use utest\templating\TemplatingTestCase;

/**
 * Тесты помощников вида для форм.
 */
class FormHelperTest extends TemplatingTestCase
{
    /**
     * @var IForm $form
     */
    protected $form;
    /**
     * @var FormHelper $helper
     */
    protected $helper;

    public function setUpFixtures()
    {
        $this->helper = new FormHelper();
        $entityFactory = new EntityFactory();
        $this->resolveOptionalDependencies($entityFactory);

        $this->form = $entityFactory
            ->createForm(
            [
                'attributes' => [
                    'name'       => 'contact',
                    'action'     => '/contact',
                    'method'     => 'post',
                    'class' => 'form-horizontal'
                ],
                'elements'   => [
                    'input'    => [
                        'type'       => Hidden::TYPE_NAME,
                        'attributes' => [
                            'data-id' => '123'
                        ]
                    ],
                    'textarea' => [
                        'type'       => Textarea::TYPE_NAME,
                        'attributes' => [
                            'data-id' => '321'
                        ]
                    ],
                    'select'   => [
                        'type'    => Select::TYPE_NAME,
                        'options' => [
                            'choices' => [
                                'val1' => 'Label 1',
                                'val2' => 'Label 2'
                            ]
                        ]
                    ],
                    'checkbox' => [
                        'type'       => Checkbox::TYPE_NAME,
                        'attributes' => [
                            'data-id' => '111'
                        ]
                    ],
                    'button'   => [
                        'type'       => Button::TYPE_NAME,
                        'label'      => 'Label',
                        'attributes' => [
                            'data-id' => '222'
                        ]
                    ],
                ]
            ]
        );
    }

    public function testOpenAndCloseTag()
    {
        $this->assertEquals(
            '<form name="contact" action="/contact" method="post" class="form-horizontal">',
            $this->helper
                ->openTag($this->form),
            'Ожидается, что будет получен открывающий тэг формы.'
        );

        $this->assertEquals(
            '</form>',
            $this->helper
                ->closeTag(),
            'Ожидается, что будет получен закрывающий тэг формы.'
        );
    }

    public function testInput()
    {
        $this->assertEquals(
            '<input data-id="123" type="hidden" name="input" value="" />',
            $this->helper
                ->formInput($this->form->get('input')),
            'Ожидается, что будет получен <input> элемент формы.'
        );
    }

    public function testTextarea()
    {
        $this->assertEquals(
            '<textarea data-id="321" name="textarea" ></textarea>',
            $this->helper
                ->formTextarea($this->form->get('textarea')),
            'Ожидается, что будет получен <textarea> элемент формы.'
        );
    }

    public function testSelect()
    {
        $this->assertEquals(
            '<select name="select" ><option value="val1">Label 1</option><option value="val2">Label 2</option></select>',
            $this->helper
                ->formSelect($this->form->get('select')),
            'Ожидается, что будет получен <select> элемент формы.'
        );
    }

    public function testElement()
    {
        $helpers = $this->helper;
        $this->assertEquals(
            $helpers->formInput($this->form->get('input')),
            $helpers->formElement($this->form->get('input')),
            'Ожидается, что будет вызван верный Helper.'
        );

        $this->assertEquals(
            $helpers->formSelect($this->form->get('select')),
            $helpers->formElement($this->form->get('select')),
            'Ожидается, что будет вызван верный Helper.'
        );

        $this->assertEquals(
            $helpers->formTextarea($this->form->get('textarea')),
            $helpers->formElement($this->form->get('textarea')),
            'Ожидается, что будет вызван верный Helper.'
        );
    }

}

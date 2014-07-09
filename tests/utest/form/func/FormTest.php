<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\func;

use umi\event\TEventObservant;
use umi\form\IForm;
use utest\form\FormTestCase;

/**
 * Тестирование форм.
 */
class FormTest extends FormTestCase
{
    /**
     * @var IForm $form форма
     */
    protected $form;

    public function setUpFixtures()
    {
        $this->form = $this->getTestToolkit()
            ->getService('umi\form\IEntityFactory')
                ->createForm(require __DIR__ . '/form.php');
    }

    /**
     * Тестирование получения элементов из формы.
     */
    public function testBasic()
    {
        $this->assertEquals(
            'register',
            $this->form->getName(),
            'Ожидается, что имя формы будет установлено.'
        );

        $this->assertInstanceOf(
            'umi\form\element\IFormElement',
            $this->form->get('email'),
            'Ожидается, что будет получен элемент формы.'
        );
        $this->assertInstanceOf(
            'umi\form\fieldset\IFieldset',
            $this->form->get('passport'),
            'Ожидается, что будет получена группа полей.'
        );

        $city = $this->form->get('passport')
            ->get('birthday_city');
        $this->assertInstanceOf(
            'umi\form\element\IFormElement',
            $city,
            'Ожидается, что будет получен элемент формы.'
        );

        $submit = $this->form->get('submit');
        $this->assertEquals(
            $submit->getLabel(),
            $this->form->get('submit')
                ->getValue(),
            'Ожидается, что значение кнопки совпадает с ее названием.'
        );
    }

    /**
     * Тестирует поведение подформ на форме.
     */
    public function testFieldSet()
    {
        $this->form->setData(
            [
                'passport' => [
                    'number'        => 123456,
                    'birthday_city' => 'Спб'
                ]
            ]
        );

        $this->assertEquals(
            [
                'number'        => 123456,
                'birthday_city' => 'Спб'
            ],
            $this->form->getData()['passport'],
            'Ожидается, что данные были установлены'
        );
    }

    /**
     * Тестирование установки данных в форму.
     */
    public function testFormsData()
    {
        $this->assertSame(
            $this->form,
            $this->form->setData(
                [
                    'email'    => 'name@example.com',
                    'password' => 'password'
                ]
            ),
            'Ожидается, что будет возвращен $this.'
        );

        $this->assertTrue(
            $this->form->isValid(),
            'Ожидается, что неполные данные формы будут верны, т.к. все обязательные элементы установлены.'
        );

        $this->assertEquals(
            [
                'email'           => 'name@example.com',
                'password'        => 'password',
                'passport'        => [
                    'number'        => null,
                    'birthday_city' => null
                ],
                'fieldset' => [
                    'fieldInFieldset' => null
                ],
                'submit'          => 'Зарегистрироваться'
            ],
            $this->form->getData(),
            'Ожидается, что будут получены установленные данные от формы.'
        );

        $rawData = [
            'email'           => 'username@example.com',
            'password'        => 'password',
            'passport'        => [
                'number'        => '00123456',
                'birthday_city' => 'Мск'
            ],
            'fieldset' => [
                'fieldInFieldset' => null
            ],
            'scans'           => 'file1'
        ];
        $this->form->setData($rawData);

        $this->assertTrue($this->form->isValid(), 'Ожидается, что данные формы верны.');
        $this->assertEquals(
            [
                'email'           => 'username@example.com',
                'password'        => 'password',
                'passport'        => [
                    'number'        => '00123456',
                    'birthday_city' => 'Мск'
                ],
                'fieldset' => [
                    'fieldInFieldset' => null
                ],
                'submit'          => 'Зарегистрироваться'
            ],
            $this->form->getData(),
            'Ожидается, что будут получены установленные данные'
        );
    }

    public function testFormValidation()
    {
        $this->form->setData(
            [
                'email'    => 'name',
                'password' => 'password'
            ]
        );

        $this->assertFalse($this->form->isValid(), 'Ожидается, что данные не пройдут валидацию.');
        $this->assertEquals(
            [
                'email'           => null,
                'password'        => 'password',
                'passport'        => [
                    'number'        => null,
                    'birthday_city' => '',
                ],
                'fieldset' => [
                    'fieldInFieldset' => null
                ],
                'submit'          => 'Зарегистрироваться'
            ],
            $this->form->getData(),
            'Ожидается, что будут получены данные прошедшие валидацию.'
        );

        $this->form->setData(
            [
                'email'    => '              name@example.ru',
                'password' => 'password'
            ]
        );

        $this->assertTrue($this->form->isValid(), 'Ожидается, что данные пройдут валидацию.');
        $this->assertEquals(
            [
                'email'           => 'name@example.ru',
                'password'        => 'password',
                'passport'        => [
                    'number'        => null,
                    'birthday_city' => '',
                ],
                'fieldset' => [
                    'fieldInFieldset' => null
                ],
                'submit'          => 'Зарегистрироваться'
            ],
            $this->form->getData(),
            'Ожидается, что будут получены данные прошедшие фильтрацию.'
        );
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\interfaces;

use umi\form\IFormEntity;
use utest\form\FormTestCase;
use utest\form\mock\interfaces\FormEntity;

/**
 * Тесты трейта "Элемент формы".
 */
class FormEntityTest extends FormTestCase
{
    /**
     * @var IFormEntity $entity элемент
     */
    public $entity;

    public function setUpFixtures()
    {
        $this->entity = new FormEntity('test');
    }

    /**
     * Тестирование аттрибутов.
     */
    public function testAttributes()
    {
        $this->assertTrue(is_array($this->entity->getAttributes()));
        $this->entity->setAttribute('testAttr', 'val');
        $this->assertEquals(
            ['testAttr' => 'val'],
            $this->entity->getAttributes(),
            'Ожидается, что аттрибуты будут установлены.'
        );
    }
}
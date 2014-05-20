<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\validation\unit\type;

use umi\validation\exception\RuntimeException;
use umi\validation\IValidatorFactory;
use umi\validation\type\Regexp;
use utest\validation\ValidationTestCase;

/**
 * Класс RegexpValidatorTests
 */
class RegexpValidatorTests extends ValidationTestCase
{

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function wrongValidatorPattern()
    {
        $validator = new Regexp(IValidatorFactory::TYPE_REGEXP);
        $validator->isValid('1234');
    }

    public function testValidate()
    {
        $validator = new Regexp(IValidatorFactory::TYPE_REGEXP, ['pattern' => '/[0-9]+/']);
        $this->assertTrue($validator->isValid("1234"), "Ожидается, что число пройдет валидацию");
        $this->assertNull($validator->getMessage(), "Ожидается, что сообщений об ошибках не будет");

        $this->assertFalse($validator->isValid("NaN"), "Ожидается, что не число не пройдет валидацию");
        $this->assertEquals(
            "String does not meet regular expression.",
            $validator->getMessage(),
            "Ожидается, что будет сообщение о неверной валидации"
        );

        $validator->isValid("1234");
        $this->assertNull($validator->getMessage(), "Ожидается, что сообщений об ошибках не будет");
    }
}
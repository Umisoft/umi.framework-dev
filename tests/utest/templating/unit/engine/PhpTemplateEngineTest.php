<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\engine;

use umi\templating\engine\php\PhpTemplateEngine;
use utest\templating\TemplatingTestCase;

/**
 * Тесты PHP шаблонизатора
 */
class PhpTemplateEngineTest extends TemplatingTestCase
{
    /**
     * @var PhpTemplateEngine $engine
     */
    protected $engine;

    public function setUpFixtures()
    {
        $this->engine = new PhpTemplateEngine();
        $this->engine->setOptions(
            [
                PhpTemplateEngine::OPTION_TEMPLATE_DIRECTORY => __DIR__ . '/data/php',
                PhpTemplateEngine::OPTION_TEMPLATE_FILE_EXTENSION => 'phtml',

            ]
        );

        $this->resolveOptionalDependencies($this->engine);
    }

    public function testRender()
    {
        $response = $this->engine->render('example', ['var' => 'testVal']);

        $this->assertEquals(
            'Hello world! testVal',
            $response,
            'Ожидается, что контент будет установлен.'
        );
    }

    public function testException()
    {
        $e = null;
        try {
            $this->engine->render('wrong', []);
        } catch (\Exception $e) {
        }

        $this->assertNotNull($e, 'Ожидается, что исключение будет брошено.');
        $this->assertNotContains('wrong', ob_get_contents(), 'Ожидается, что буффер будет очищен.');
    }

    public function testPartial()
    {
        $response = $this->engine->render('partial', []);

        $this->assertEquals(
            'Partial: Hello world! test',
            $response,
            'Ожидается, что mock будет вызван.'
        );
    }
}
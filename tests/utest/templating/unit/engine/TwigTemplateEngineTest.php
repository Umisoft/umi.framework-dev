<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\engine;

use umi\extension\twig\TwigTemplateEngine;
use utest\templating\TemplatingTestCase;

/**
 * Тесты Twig шаблонизатора.
 */
class TwigTemplateEngineTest extends TemplatingTestCase
{
    /**
     * @var TwigTemplateEngine $engine
     */
    protected $engine;

    public function setUpFixtures()
    {
        $this->engine = new TwigTemplateEngine();
        $this->engine->setOptions(
            [
                TwigTemplateEngine::OPTION_TEMPLATE_DIRECTORY => __DIR__ . '/data/twig',
                TwigTemplateEngine::OPTION_TEMPLATE_FILE_EXTENSION => 'twig',
            ]
        );
        $this->resolveOptionalDependencies($this->engine);
    }

    public function testRender()
    {
        $response = $this->engine->render(
            'example',
            [
                'var' => 'testVal'
            ]
        );

        $this->assertEquals(
            'Hello world! testVal',
            $response,
            'Ожидается, что контент будет установлен.'
        );
    }

}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\monolog\unit;

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Logger;
use umi\extension\monolog\toolbox\MonologTools;
use utest\monolog\MonologTestCase;

/**
 * Тесты инструментов логирования
 */
class MonologToolsTests extends MonologTestCase
{
    /**
     * @var MonologTools $loggerTools
     */
    protected $monologTools;

    protected function setUpFixtures()
    {
        $this->monologTools = new MonologTools();
        $this->resolveOptionalDependencies($this->monologTools);
    }

    public function testArrayConfigLogger()
    {
        $logger = $this->monologTools->getService('Psr\Log\LoggerInterface', null);
        $this->assertInstanceOf(
            'Psr\Log\LoggerInterface',
            $logger,
            'MonologTools::getLogger() должен вернуть Psr\Log\LoggerInterface'
        );
        $this->assertTrue(
            $logger === $this->monologTools->getService('Psr\Log\LoggerInterface', null),
            'Ожидается, что у инструментария логирования только один логер'
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWrongConfig()
    {
        $this->monologTools->default = 'WrongConfig';
        $this->monologTools->getService('Psr\Log\LoggerInterface', null);
    }

    public function testStreamHandler()
    {
        $logFile = __DIR__ . '/test.log';
        @unlink($logFile);
        $this->monologTools->default = [
            'handlers' => [
                ['type' => 'stream', 'path' => $logFile, 'level' => Logger::DEBUG]
            ]
        ];
        /** @var $logger Logger */
        $logger = $this->monologTools->getService('Psr\Log\LoggerInterface', null);
        $logger->log(Logger::DEBUG, 'test');

        /** @var $handler StreamHandler */
        $handler = $logger->popHandler();
        $this->assertInstanceOf(
            'Monolog\Handler\StreamHandler',
            $handler,
            "Config must produce Stream handler"
        );

        $this->assertFileExists($logFile, "Configured log file must be created");
    }

    public function testFirePhpHandler()
    {
        $this->monologTools->default = [
            'handlers' => [
                ['type' => 'firephp', 'level' => Logger::ERROR]
            ]
        ];
        /** @var $logger Logger */
        $logger = $this->monologTools->getService('Psr\Log\LoggerInterface', null);
        $logger->log(Logger::ERROR, 'fire!');

        /** @var $handler FirePHPHandler */
        $handler = $logger->popHandler();
        $this->assertInstanceOf(
            'Monolog\Handler\FirePHPHandler',
            $handler,
            "Config must produce FirePHPHandler handler"
        );
    }

    public function testSwiftMailerHandler()
    {
        $this->monologTools->default = [
            'handlers' => [
                [
                    'type'       => 'swift_mailer',
                    'level'      => Logger::ERROR,
                    'from_email' => 'foo@bar',
                    'to_email'   => 'bar@foo',
                    'subject'    => 'Test',
                    'transport_params'    => '',
                ]
            ]
        ];
        /** @var $logger Logger */
        $logger = $this->monologTools->getService('Psr\Log\LoggerInterface', null);
        $logger->log(Logger::ERROR, 'fire!');

        /** @var $handler SwiftMailerHandler */
        $handler = $logger->popHandler();
        $this->assertInstanceOf(
            'Monolog\Handler\SwiftMailerHandler',
            $handler,
            "Config must produce FirePHPHandler handler"
        );
    }
}

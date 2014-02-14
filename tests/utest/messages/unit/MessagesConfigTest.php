<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\messages\unit;

use umi\messages\exception\InvalidArgumentException;
use utest\messages\mock\SwiftMailerAware;

/**
 * Тесты на работоспособность конфигурации инструментов службы сообщений
 */
class MessagesConfigTest extends MessageTestCase
{
    /**
     * @dataProvider provideValidConfigs
     */
    public function testValidConfigs($config)
    {
        $this->messagesTools->mailerOptions = $config;
        $this->assertInstanceOf(
            'umi\messages\ISwiftMailer',
            $this->messagesTools->getService('umi\messages\SwiftMailer', null),
            'ISwiftMailer must be created on valid config'
        );
    }

    /**
     * @dataProvider provideInvalidConfigs
     * @expectedException InvalidArgumentException
     */
    public function testInvalidConfigs($config)
    {
        $this->messagesTools->mailerOptions = $config;
        $this->messagesTools->getService('umi\messages\SwiftMailer', null);
    }

    /**
     * Valid configs for MessagesTools
     *
     * @return array
     */
    public function provideValidConfigs()
    {
        $base = ['sender_address' => ['foo@bar.com'], 'delivery_address' => ['sir@foo.com']];
        return [
            [
                array_merge($base, ['transport' => 'mail'])
            ],
            [
                array_merge($base, ['transport' => 'sendmail'])
            ],
            [
                array_merge($base, ['transport' => 'smtp', 'host' => '127.0.0.1'])
            ],
        ];
    }

    /**
     * Valid configs for MessagesTools
     *
     * @return array
     */
    public function provideInvalidConfigs()
    {
        $base = ['sender_address' => ['foo@bar.com'], 'delivery_address' => ['sir@foo.com']];
        return [
            [
                array_merge($base, ['transport' => 'foo']),
            ],
            [
                array_merge($base, ['transport' => 'smtp']),
            ],
            [
                ['transport' => 'mail'],
            ],
        ];
    }

    public function testMailerAware()
    {
        $aw = new SwiftMailerAware();
        $this->resolveOptionalDependencies($aw);

        try {
            $aw->testSend('foo', 'bodyfoo', 'text/html');
        } catch (\Exception $e) {
            $this->assertInstanceOf(
                'umi\messages\exception\FailedRecipientsException',
                $e,
                'fake addresses must raise FailedRecipientsException'
            );
        }
    }
}

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
use utest\messages\mock\TestTransport;

/**
 * Тесты на работоспособность конфигурации инструментов службы сообщений
 */
class MessagesConfigTest extends MessageTestCase
{
    /**
     * @dataProvider provideValidConfigs
     */
    public function testValidConfigs($mailerOptions, $transportOptions)
    {
        $this->messagesTools->mailerOptions = $mailerOptions;
        $this->messagesTools->transportOptions = $transportOptions;
        $this->assertInstanceOf(
            'umi\messages\SwiftMailer',
            $this->messagesTools->getService('umi\messages\SwiftMailer', null),
            'SwiftMailer must be created on valid mailerOptions'
        );
    }

    /**
     * @dataProvider provideInvalidConfigs
     * @expectedException InvalidArgumentException
     */
    public function testInvalidConfigs($mailerOptions, $transportOptions)
    {
        $this->messagesTools->mailerOptions = $mailerOptions;
        $this->messagesTools->transportOptions = $transportOptions;
        $this->messagesTools->getService('umi\messages\SwiftMailer', null);
    }

    /**
     * Valid configs for MessagesTools
     *
     * @return array
     */
    public function provideValidConfigs()
    {
        $base = ['sender_address' => [], 'delivery_address' => []];
        return [
            [
                array_merge($base, ['transport' => 'mail']),
                []
            ],
            [
                array_merge($base, ['transport' => 'sendmail']),
                []
            ],
            [
                array_merge($base, ['transport' => 'smtp',]),
                ['smtp' => ['host' => '127.0.0.1']]
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
        $base = ['sender_address' => [], 'delivery_address' => []];
        return [
            [
                array_merge($base, ['transport' => 'foo']),[]
            ],
            [
                array_merge($base, ['transport' => 'smtp']),[]
            ],
            [
                ['transport' => 'mail'],[]
            ],
        ];
    }

    public function testMailerAware()
    {
        $swiftMailerAware = new SwiftMailerAware();
        $transport = new TestTransport();
        $this->messagesTools->setTransport($transport);
        $swiftMailerAware->setSwiftMailer($this->messagesTools->getService('umi\messages\SwiftMailer', null));

        try {
            $swiftMailerAware->testSend('foo', 'bodyfoo', 'text/html');
        } catch (\Exception $e) {
            $this->assertInstanceOf(
                'umi\messages\exception\FailedRecipientsException',
                $e,
                'fake addresses must raise FailedRecipientsException'
            );
        }
    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\messages\unit;

use utest\messages\mock\TestTransport;

/**
 * Тесты на работу транспорта почтовой службы
 */
class MessageTransportTest extends MessageTestCase
{
    public function testAttaches()
    {
        $attach = __DIR__ . '/../data/attach-plain.txt';
        $transport = new TestTransport();

        $this->messagesTools->setTransport($transport);

        $this->messagesTools->getService('umi\messages\SwiftMailer', null)
            ->sendMail('test', 'testbody', 'text/plain', [$attach]);


        $this->assertContains('name=attach-plain.txt', $transport->getSentContent(), "Attach must present in mail body");
    }

    protected function tearDownFixtures()
    {
        parent::tearDownFixtures();
        if ($tmpFiles = glob(__DIR__ . '/tmp/*')) {
            foreach ($tmpFiles as $f) {
                unlink($f);
            }
        }
    }
}

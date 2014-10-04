<?php
/**
 * This file is part of UMI.CMS.
 *
 * @link http://umi-cms.ru
 * @copyright Copyright (c) 2007-2014 Umisoft ltd. (http://umisoft.ru)
 * @license For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace utest\messages\unit;

use utest\messages\mock\SwiftMailerAware;
use utest\messages\mock\TestTransport;

/**
 * Тест классов, отправителей сообщений.
 */
class MailerAwareTest extends MessageTestCase
{
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
 
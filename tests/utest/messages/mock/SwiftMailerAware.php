<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\messages\mock;

use umi\messages\ISwiftMailerAware;
use umi\messages\TSwiftMailerAware;

/**
 * Тестовый клиент для трейта TSwiftMailerAware.
 */
class SwiftMailerAware implements ISwiftMailerAware
{
    use TSwiftMailerAware;

    /**
     * Публичный метод для теста
     * @param $subject
     * @param $body
     * @param $contentType
     * @param array $files
     * @param null $to
     * @param null $from
     * @param string $charset
     */
    public function testSend(
        $subject,
        $body,
        $contentType,
        array $files = [],
        $to = null,
        $from = null,
        $charset = 'utf-8'
    ) {
        $this->sendMail($subject, $body, $contentType, $files, $to, $from, $charset);
    }
}

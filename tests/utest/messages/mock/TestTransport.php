<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\messages\mock;

use Swift_Events_EventListener;
use Swift_Mime_Message;

/**
 * Тестовый почтовы транспорт для отладки почтовой службы
 */
class TestTransport implements \Swift_Transport
{
    private $isStarted = false;

    private $sentContent = '';

    /**
     * Возвращает содержимое последнего отправленного письма
     * @return string
     */
    public function getSentContent()
    {
        return $this->sentContent;
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        return $this->isStarted;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $this->isStarted = true;
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $this->isStarted = false;
    }

    /**
     * Сохраняет отправленное письмо во временную папку
     *
     * @param Swift_Mime_Message $message
     * @param string[] $failedRecipients An array of failures by-reference
     * @return integer
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $this->sentContent = $message->toString();

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        return $this;
    }

}

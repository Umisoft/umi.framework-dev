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
    /**
     * @var string $dumpPath
     */
    private $dumpPath;

    /**
     * @param string $dumpPath Where to save dumped messages
     */
    public function __construct($dumpPath)
    {
        if (!is_dir($dumpPath)) {
            mkdir($dumpPath, 0777, true);
        }
        $this->dumpPath = $dumpPath;
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
        $filename = $this->escape($message->getSubject()) . '.txt';
        file_put_contents($this->dumpPath . '/' . $filename, $message->toString());
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        return $this;
    }

    /**
     * Экранирование строки для использования в качестве пути к файлу
     * @param string $string
     * @return string
     */
    protected function escape($string)
    {
        $string = transliterator_transliterate(
            "Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();",
            $string
        );
        $string = preg_replace('/[-\s]+/', '-', $string);
        return trim($string, '-');
    }
}

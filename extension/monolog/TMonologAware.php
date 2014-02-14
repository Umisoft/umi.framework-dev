<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\extension\monolog;

use Monolog\Logger;

/**
 * Трейт для поддержки логирования через библиотеку Monolog {@link https://github.com/Seldaek/monolog}
 */
trait TMonologAware
{
    /**
     * @var \Monolog\Logger $logger логгер
     */
    private $traitLogger;

    /**
     * Внедряет логгер.
     * @param \Monolog\Logger $logger логгер
     */
    public function setLogger(Logger $logger)
    {
        $this->traitLogger = $logger;
    }

    /**
     * Записывает сообщение в лог.
     * @param string $level уровень критичности сообщения
     * @param string $message сообщение, поддерживает плейсхолдеры в формате {placeholder}
     * @param array $placeholders список плейсхолдеров
     * @return $this
     */
    protected function log($level, $message, array $placeholders = [])
    {
        if ($this->traitLogger) {
            $this->traitLogger->log($level, $message, $placeholders);
        }

        return $this;
    }

    /**
     * Pаписывает сообщение для отладки в лог (level = LOG_DEBUG)
     * @param string $message сообщение, поддерживает плейсхолдеры в формате {placeholder}
     * @param array $placeholders список плейсхолдеров
     * @return $this
     */
    protected final function trace($message, array $placeholders = [])
    {
        return $this->log(Logger::DEBUG, $message, $placeholders);
    }
}

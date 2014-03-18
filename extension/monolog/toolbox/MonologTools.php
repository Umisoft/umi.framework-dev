<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\extension\monolog\toolbox;

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;
use Traversable;
use umi\extension\monolog\exception\RuntimeException;
use umi\messages\ISwiftMailerAware;
use umi\messages\TSwiftMailerAware;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов логирования.
 * Логеры конфигурируются и вызываются поименно, каждый содержит свой набор обработчиков и постпроцессоров.
 */
class MonologTools implements IToolbox, ISwiftMailerAware
{
    use TToolbox;
    use TSwiftMailerAware;

    /**
     * Имя набора инструментов.
     */
    const NAME = 'monolog';

    /**
     * Конфигурация логеров поименно, каждому можно указать обработчики и постпроцессоры
     * @var array $loggers
     */
    public $default = [
        'handlers' => [],
        'processors' => []
    ];

    /**
     * Опции логгера
     * @var array $options
     */
    public $options = [];

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'Psr\Log\LoggerInterface':
                return $this->getLogger();
        }
        throw new UnsupportedServiceException($this->translate(
            'Toolbox "{name}" does not support service "{interface}".',
            ['name' => self::NAME, 'interface' => $serviceInterfaceName]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof LoggerAwareInterface) {
            $object->setLogger($this->getLogger());
        }
    }

    /**
     * Возвращает экземпляр логгера.
     * @return Logger
     */
    protected function getLogger()
    {
        $prototype = $this->getPrototype('Monolog\Logger', ['Psr\Log\LoggerInterface']);

        return $prototype->createSingleInstance(
            ['default'],
            [],
            function (Logger $logger) {
                if (!is_array($this->default) && (!$this->default instanceof Traversable)) {
                    throw new RuntimeException("Config must be array or Traversable");
                }
                $handlerConfigs = $this->default['handlers'];
                foreach ($handlerConfigs as $config) {
                    $logger->pushHandler($this->createHandler($config));
                }
            }
        );
    }

    /**
     * Создает обработчик логирования.
     * @param array|Traversable $config
     * @throws RuntimeException
     * @return HandlerInterface
     */
    private function createHandler($config)
    {
        if (!is_array($config) && (!$config instanceof Traversable)) {
            throw new RuntimeException("Config must be array or Traversable");
        }
        if (!isset($config['type'])) {
            throw new RuntimeException("No handler type specified");
        }
        $bubble = isset($config['bubble']) ? (bool) $config['bubble'] : true;
        switch ($config['type']) {
            case 'stream':
                $handler = new StreamHandler($config['path'], $config['level'], $bubble);
                break;
            case 'firephp':
                $handler = new FirePHPHandler($config['level'], $bubble);
                break;
            case 'swift_mailer':
                $mailer = $this->getSwiftMailer();
                $message = \Swift_Message::newInstance($config['subject']);
                $message->setFrom($config['from_email']);
                $message->setTo($config['to_email']);
                $handler = new SwiftMailerHandler(
                    $mailer,
                    $message,
                    $config['level'],
                    $bubble
                );
                break;
            default:
                throw new RuntimeException("Unsupported type {$config['type']}");
        }
        return $handler;
    }
}

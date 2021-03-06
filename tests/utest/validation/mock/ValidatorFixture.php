<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\validation\mock;

use umi\validation\IValidator;

/**
 * Класс ValidatorFixture
 */
class ValidatorFixture implements IValidator
{

    /**
     * @var array $options опции валидатора
     */
    protected $options;
    /**
     * @var string $type тип валидатора
     */
    protected $type;
    /**
     * @var array $message сообщение об ошибках
     */
    protected $message;
    /**
     * @var bool $isValid "валидность" валидатора
     */
    protected $isValid;

    /**
     * Конструктор.
     * @param string $type тип валидатора
     * @param array $options опции валидатора
     */
    public function __construct($type, array $options)
    {
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorLabel()
    {
        return 'Invalid validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($var)
    {
        $this->message = null;

        if (isset($this->options['is_valid'])) {
            if (!$this->options['is_valid']) {
                $this->message = $this->getErrorLabel();

                return false;
            } else {
                return $this->options['is_valid'];
            }

        } else {
            throw new \RuntimeException("No 'is_valid' option");
        }
    }
}
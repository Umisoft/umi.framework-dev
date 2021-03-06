<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\validation;

/**
 * Интерфейс валидации.
 */
interface IValidator
{
    /**
     * Возвращает тип валидатора.
     * @return string
     */
    public function getType();

    /**
     * Возвращает опции валидации.
     * @return array
     */
    public function getOptions();

    /**
     * Возвращает сообщение ошибки валидации.
     * @return string
     */
    public function getMessage();

    /**
     * Возвращает текст для генерации ошибки.
     * @return string
     */
    public function getErrorLabel();

    /**
     * Проверяет, подходит ли значение для данного валидатора.
     * @param mixed $value валидируемое значение
     * @return bool
     */
    public function isValid($value);
}
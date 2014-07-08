<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\mock\interfaces;

use umi\form\adapter\DefaultFormAdapter;
use umi\form\BaseFormEntity as FrameworkBaseFormEntity;

/**
 * Мок-класс элемента формы.
 */
class FormEntity extends FrameworkBaseFormEntity
{

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataAdapter()
    {
        return new DefaultFormAdapter();
    }

    /**
     * {@inheritdoc}
     */
    public function getIsSubmitted()
    {
        return false;
    }
}
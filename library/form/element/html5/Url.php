<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\element\html5;

use umi\form\element\BaseFormElement;
use umi\form\element\IFormInput;

/**
 * HTML5 элемент формы - URL.
 * @example <input type="url" />
 */
class Url extends BaseFormElement implements IFormInput
{
    /**
     * Тип элемента.
     */
    const TYPE_NAME = 'url';

    /**
     * {@inheritdoc}
     */
    public function getInputType()
    {
        return self::TYPE_NAME;
    }
}
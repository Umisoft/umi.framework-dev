<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\extension\twig;

use Twig_Extension;
use Twig_SimpleFunction;
use umi\templating\helper\pagination\PaginationHelper;

/**
 * Расширение Twig для подключения помощников шаблонов.
 */
class TemplatingTwigExtension extends Twig_Extension
{

    /**
     * @var string $paginationFunctionName имя функции для генерации постраничной навигации
     */
    public $paginationFunctionName = 'pagination';
    /**
     * @var string $translateFunctionName имя функции для перевода
     */
    public $translateFunctionName = 'translate';

    /**
    * {@inheritdoc}
    */
    public function getName()
    {
        return __CLASS__;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                $this->paginationFunctionName,
                $this->getPaginationHelper()
            )
        ];
    }

    /**
     * Возвращает помощник шаблонов для вывода постраничной навигации.
     * @return callable
     */
    protected function getPaginationHelper()
    {
        return function() {
            static $helper;

            if (!$helper) {
                $helper = new PaginationHelper();
            }

            return $helper;
        };
    }

}
 
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\extension\twig;

use Twig_Environment;
use Twig_Extension;
use Twig_Loader_Filesystem;
use umi\templating\engine\ITemplateEngine;

/**
 * Twig шаблонизатор.
 */
class TwigTemplateEngine implements ITemplateEngine
{
    /**
     * Имя
     */
    const NAME = 'twig';
    /**
     * Опция для задания директорий расположения шаблонов
     */
    const OPTION_TEMPLATE_DIRECTORIES = 'directories';
    /**
     * Опция для задания расширения файлов шаблонов
     */
    const OPTION_TEMPLATE_FILE_EXTENSION = 'extension';
    /**
     * Опции окружения Twig
     */
    const OPTION_ENVIRONMENT = 'environment';

    /**
     * @var array $options опции
     */
    protected $options = [];
    /**
     * @var Twig_Environment $environment окружение шаблонизатора Twig
     */
    private $environment;

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render($templateName, array $arguments = [])
    {
        return $this->getEnvironment()->render(
            $this->getTemplateFilename($templateName),
            $arguments
        );
    }

    /**
     * Добавляет расширение.
     * @param Twig_Extension $extension
     * @return $this
     */
    public function addExtension(Twig_Extension $extension)
    {
        $this->getEnvironment()->addExtension($extension);

        return $this;
    }

    /**
     * Возвращает конфигурацию Twig.
     * @return Twig_Environment
     */
    protected function getEnvironment()
    {
        if (!$this->environment) {
            $templateDirectories = isset($this->options[self::OPTION_TEMPLATE_DIRECTORIES]) ? $this->options[self::OPTION_TEMPLATE_DIRECTORIES] : [];

            $environmentOptions = [];
            if (isset($this->options[self::OPTION_ENVIRONMENT]) && is_array($this->options[self::OPTION_ENVIRONMENT])) {
                $environmentOptions = $this->options[self::OPTION_ENVIRONMENT];
            }

            $twigLoader = new Twig_Loader_Filesystem($templateDirectories);
            $this->environment = new Twig_Environment($twigLoader, $environmentOptions);
        }

        return $this->environment;
    }

    /**
     * Возрващает имя файла шаблона по имени шаблона.
     * @param string $templateName имя шаблона
     * @return string
     */
    protected function getTemplateFilename($templateName)
    {
        if (isset($this->options[self::OPTION_TEMPLATE_FILE_EXTENSION])) {
            $templateName .= '.' . $this->options[self::OPTION_TEMPLATE_FILE_EXTENSION];
        }

        return $templateName;
    }

}
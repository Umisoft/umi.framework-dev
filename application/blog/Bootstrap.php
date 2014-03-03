<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

use umi\config\entity\IConfig;
use umi\config\io\IConfigIO;
use umi\extension\twig\TemplatingTwigExtension;
use umi\extension\twig\ViewTwigExtension;
use umi\hmvc\component\IComponent;
use umi\hmvc\dispatcher\IDispatcher;
use umi\hmvc\IMvcEntityFactory;
use umi\http\Request;
use umi\http\Response;
use umi\spl\config\TConfigSupport;
use umi\templating\engine\ITemplateEngineFactory;
use umi\extension\twig\TwigTemplateEngine;
use umi\toolkit\IToolkit;
use umi\toolkit\Toolkit;

/**
 * Класс Bootstrap. Инициализирует приложение.
 */
class Bootstrap
{
    const OPTION_TOOLKIT = 'toolkit';
    const OPTION_SETTINGS = 'settings';
    /**
     * Символическое имя конфигурационного файла.
     */
    const GENERAL_CONFIG = '~/general.php';

    use TConfigSupport;

    /**
     * @var IToolkit $tools
     */
    protected $toolkit;
    /**
     * @var IConfig $config
     */
    protected $configuration;

    /**
     * Конструктор
     * @param array $configuration boot-конфигурация
     */
    public function __construct(array $configuration)
    {
        $this->initToolkit($configuration);
    }

    /**
     * Запускает приложение.
     */
    public function runApplication()
    {
        /**
         * @var IDispatcher $dispatcher
         */
        $dispatcher = $this->toolkit->getService('umi\hmvc\dispatcher\IDispatcher');
        $this->initTemplateEngines($dispatcher);

        /**
         * @var Request $request
         */
        $request = $this->toolkit->getService('umi\http\Request');
        $this->validateRequest($request);

        $application = $this->createApplication();

        try {
            $dispatcher->dispatch($application, $request);
        } catch (\Exception $e) {
            throw new ErrorException(
                'Unhandled exception thrown.', 0, 1, __FILE__, __LINE__, $e
            );
        }
    }

    /**
     * Возвращает toolkit.
     * @return IToolkit
     */
    protected function getToolkit()
    {
        return $this->toolkit;
    }

    /**
     * Создает компонент приложения.
     * @return IComponent
     */
    protected function createApplication()
    {
        $appConfig = $this->configToArray($this->configuration->get('application'));

        /**
         * @var IMvcEntityFactory $mvcEntityFactory
         */
        $mvcEntityFactory = $this->toolkit->getService('umi\hmvc\IMvcEntityFactory');

        return $mvcEntityFactory->createComponent('application', 'application', $appConfig);
    }

    /**
     * Задает инициализаторы для добавления расширений в шаблонизаторы
     * @param IDispatcher $dispatcher
     */
    protected function initTemplateEngines(IDispatcher $dispatcher)
    {
        /**
         * @var ITemplateEngineFactory $templateEngineFactory
         */
        $templateEngineFactory = $this->toolkit->getService('umi\templating\engine\ITemplateEngineFactory');
        $templateEngineFactory->setInitializer(
            TwigTemplateEngine::NAME,
            function (TwigTemplateEngine $templateEngine) use ($dispatcher) {

                $viewExtension = new ViewTwigExtension($dispatcher);
                $templateExtension = new TemplatingTwigExtension();

                $templateEngine
                    ->addExtension($viewExtension)
                    ->addExtension($templateExtension);
            }
        );
    }

    /**
     * Загружает и регистрирует конфигурацию тулбокса.
     */
    protected function initToolkit($bootConfig)
    {
        $this->toolkit = new Toolkit();

        if (isset($bootConfig[self::OPTION_TOOLKIT])) {
            $this->toolkit->registerToolboxes($bootConfig[self::OPTION_TOOLKIT]);
        }

        if (isset($bootConfig[self::OPTION_SETTINGS])) {
            $this->toolkit->setSettings($bootConfig[self::OPTION_SETTINGS]);
        }

        /**
         * @var IConfigIO $configIO
         */
        $configIO = $this->toolkit->getService('umi\config\io\IConfigIO');

        $this->configuration = $configIO->read(self::GENERAL_CONFIG);
        $this->toolkit->setSettings($this->configuration['toolkit'] ? : []);
    }

    /**
     * Проверяет необходимость редиректа и выполняет его
     * в случае наличия лишних слешей в запросе.
     * @param Request $request
     */
    protected function validateRequest(Request $request)
    {
        $pathInfo = $request->getPathInfo();

        if ($pathInfo != '/' && substr($pathInfo, -1, 1) == '/') {

            $url = rtrim($pathInfo, '/');
            if ($queryString = $request->getQueryString()) {
                $url .= '?' . $queryString;
            }
            /**
             * @var Response $response
             */
            $response = $this->toolkit->getService('umi\http\Response');
            $response->setStatusCode(Response::HTTP_MOVED_PERMANENTLY)
                ->headers->set('Location',  $request->getSchemeAndHttpHost() . $url);

            $response->send();
            exit();
        }
    }

}

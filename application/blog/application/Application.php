<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace application;

use umi\hmvc\component\Component;
use umi\hmvc\dispatcher\IDispatchContext;
use umi\http\Request;
use umi\http\Response;
use umi\i18n\ILocalesAware;
use umi\i18n\TLocalesAware;
use umi\session\ISession;
use umi\session\ISessionAware;
use umi\session\TSessionAware;

/**
 * MVC Application.
 */
class Application extends Component implements ISessionAware, ILocalesAware
{
    use TLocalesAware;

    /**
     * @var ISession $session
     */
    protected $session;

    /**
     * {@inheritdoc}
     */
    public function setSessionService(ISession $sessionService)
    {
        $this->session = $sessionService;
    }

    /**
     * {@inheritdoc}
     */
    public function onDispatchResponse(IDispatchContext $context, Response $response)
    {
        if ($this->session) {
            $this->session->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onDispatchRequest(IDispatchContext $context, Request $request)
    {

        $routeParams = $context->getRouteParams();

        if (isset($routeParams['locale'])) {
            $this->setCurrentLocale($routeParams['locale']);
        }
    }
}

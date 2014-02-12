<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace application\controller;

use umi\hmvc\controller\BaseController;
use umi\http\Response;

/**
 * Контроллер сетки приложения.
 */
class LayoutController extends BaseController
{

    /**
     * @var Response $response HTTP-ответ компонента
     */
    protected $response;

    /**
     * Конструктор.
     * @param Response $response HTTP-ответ компонента
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {

        $response = $this->createViewResponse(
            'layout',
            [
                'content' => $this->response->getContent()
            ]
        );

        $response->setStatusCode($this->response->getStatusCode());
        $response->headers->replace($this->response->headers->all());

        return $response;
    }
}

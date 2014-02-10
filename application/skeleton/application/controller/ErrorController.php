<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace application\controller;

use umi\hmvc\controller\BaseController;
use umi\hmvc\exception\http\HttpException;
use umi\hmvc\exception\http\HttpNotFound;
use umi\http\Response;

/**
 * Контроллер ошибок компонента.
 */
class ErrorController extends BaseController
{
    protected $exception;

    /**
     * Конструктор.
     * @param \Exception $e
     */
    public function __construct(\Exception $e)
    {
        $this->exception = $e;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        if ($this->exception instanceof HttpNotFound) {
            return $this->error404();
        }

        $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        if ($this->exception instanceof HttpException) {
            $code = $this->exception->getCode();
        }

        return $this->createViewResponse(
            'error',
            [
                'e' => $this->exception
            ]
        )
            ->setStatusCode($code);
    }

    /**
     * Отображает 404 ошибку.
     * @return Response
     */
    public function error404()
    {
        return $this->createViewResponse(
            'error404',
            [
                'e' => $this->exception
            ]
        )
            ->setStatusCode($this->exception->getCode());
    }
}
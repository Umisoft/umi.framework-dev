<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace application\components\blog\controller;

use application\components\blog\model\PostModel;
use umi\hmvc\controller\BaseController;
use umi\hmvc\exception\http\HttpNotFound;
use umi\orm\exception\NonexistentEntityException;

/**
 * Контроллер отображения страницы поста блога.
 */
class PostController extends BaseController
{
    /**
     * @var PostModel $postModel модель постов
     */
    protected $postModel;

    /**
     * Конструктор.
     * @param PostModel $postModel модель постов
     */
    public function __construct(PostModel $postModel)
    {
        $this->postModel = $postModel;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $guid = $this->getRouteVar('id');

        try {
            $post = $this->postModel->getPost($guid);
        } catch (NonexistentEntityException $e) {
            throw new HttpNotFound('Post not found.', 0, $e);
        }

        return $this->createViewResponse(
            'post',
            [
                'post' => $post
            ]
        );
    }
}

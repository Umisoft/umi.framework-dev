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
use umi\form\IFormAware;
use umi\form\TFormAware;
use umi\hmvc\controller\BaseController;
use umi\pagination\IPaginationAware;
use umi\pagination\TPaginationAware;

/**
 * Контроллер отображения списка постов блога.
 */
class IndexController extends BaseController implements IFormAware, IPaginationAware
{
    use TFormAware;
    use TPaginationAware;

    /**
     * Количество выводимых постов на странице.
     */
    const POSTS_PER_PAGE = 3;

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
        $deleteForm = $this->createForm(require dirname(__DIR__) . '/form/deletePost.php');
        $deleteForm->getAttributes()['action'] = $this->getUrl('deletePost');

        $posts = $this->postModel->getPosts();

        $paginator = $this->createObjectPaginator($posts, self::POSTS_PER_PAGE);

        if ($paginator->getItemsCount()) {
            $paginator->setCurrentPage($this->getRouteVar('page', 1));
        }

        return $this->createViewResponse(
            'index',
            [
                'paginator'  => $paginator,
                'deleteForm' => $deleteForm
            ]
        );
    }

    /**
     * Формирует URL комопнента.
     * @param string $name имя маршрута
     * @param array $params параметры маршрута
     * @return string
     */
    protected function getUrl($name, array $params = [])
    {
        return $this->getContext()->getComponent()->getRouter()
            ->assemble($name, $params);
    }
}

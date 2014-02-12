<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace application\components\feedback\controller;

use application\components\feedback\model\ContactModel;
use application\model\UserModel;
use umi\form\IForm;
use umi\form\IFormAware;
use umi\form\TFormAware;
use umi\hmvc\controller\BaseController;
use umi\hmvc\model\IModelAware;
use umi\hmvc\model\TModelAware;
use umi\http\Response;

/**
 * Контроллер отображения формы обратной связи.
 */
class IndexController extends BaseController implements IFormAware, IModelAware
{
    use TFormAware;
    use TModelAware;

    /**
     * @var UserModel $userModel модель для работы с пользователями
     */
    protected $userModel;

    /**
     * Конструктор.
     * @param UserModel $userModel
     */
    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $form = $this->createForm(require dirname(__DIR__) . '/form/contact.php');

        if ($this->isRequestMethodPost()) {
            $form->setData($this->getAllPostVars());

            if ($form->isValid()) {
                $data = $form->getData();
                /**
                 * @var ContactModel $contactModel
                 */
                $contactModel = $this->createModelByName('contact');
                $contactModel->sendContact($data);

                return $this->createViewResponse('complete', []);
            }

            return $this->showForm($form);
        }

        if ($this->userModel->isAuthenticated()) {
            $user = $this->userModel->getCurrentUser();
            $form->getElement('name')
                ->setValue($user->name);
            $form->getElement('email')
                ->setValue($user->email);
        }

        return $this->showForm($form);
    }

    /**
     * Отображает форму обратной связи.
     * @param IForm $form
     * @return Response
     */
    public function showForm(IForm $form)
    {
        return $this->createViewResponse('index', ['form' => $form]);
    }
}

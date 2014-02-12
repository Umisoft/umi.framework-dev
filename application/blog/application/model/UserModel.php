<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace application\model;

use umi\authentication\exception\RuntimeException;
use umi\authentication\IAuthenticationAware;
use umi\authentication\IAuthenticationFactory;
use umi\authentication\TAuthenticationAware;
use umi\hmvc\model\IModel;

/**
 * Модель пользователей.
 */
class UserModel implements IAuthenticationAware, IModel
{
    use TAuthenticationAware;

    /**
     * Производит попытку авторизации в системе.
     * @param string $email email пользователя
     * @param string $password пароль
     * @return bool результат авторизации
     */
    public function login($email, $password)
    {
        if ($this->isAuthenticated()) {
            return false;
        }

        $provider = $this->createAuthProvider(
            IAuthenticationFactory::PROVIDER_SIMPLE,
            [$email, $password]
        );

        return $this->getDefaultAuthManager()
            ->authenticate($provider)
            ->isSuccessful();
    }

    /**
     * Возвращает авторизованного пользователя.
     * @throws RuntimeException если пользователь не был авторизован
     * @return object авторизованный пользователь.
     */
    public function getCurrentUser()
    {
        return $this->getDefaultAuthManager()
            ->getStorage()
            ->getIdentity();
    }

    /**
     * Проверяет, авторизован ли пользователь в системе.
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->getDefaultAuthManager()
            ->isAuthenticated();
    }

    /**
     * Уничтожает данные текущей авторизации.
     * @return void
     */
    public function logout()
    {
        $this->getDefaultAuthManager()
            ->forget();
    }

}
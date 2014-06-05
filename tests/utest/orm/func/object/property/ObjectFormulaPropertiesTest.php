<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\object\property;

use umi\orm\collection\ICollectionFactory;
use utest\orm\ORMDbTestCase;

/**
 * Тесты для вычисляемых при сохранении свойств объекта
 */
class ObjectFormulaPropertiesTest extends ORMDbTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getCollectionConfig()
    {
        return [
            self::METADATA_DIR . '/mock/collections',
            [
                self::SYSTEM_HIERARCHY       => [
                    'type' => ICollectionFactory::TYPE_COMMON_HIERARCHY
                ],
                self::BLOGS_BLOG             => [
                    'type'      => ICollectionFactory::TYPE_LINKED_HIERARCHIC,
                    'class'     => 'utest\orm\mock\collections\BlogsCollection',
                    'hierarchy' => self::SYSTEM_HIERARCHY
                ],
                self::BLOGS_POST             => [
                    'type'      => ICollectionFactory::TYPE_LINKED_HIERARCHIC,
                    'hierarchy' => self::SYSTEM_HIERARCHY
                ],
                self::USERS_USER             => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                self::USERS_GROUP            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ]
            ],
            true
        ];
    }

    public function testObject()
    {
        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);

        $user = $userCollection->add('supervisor');
        $user->setValue('firstName', 'Name');
        $user->setValue('lastName', 'LastName');

        $this->getObjectPersister()->commit();
        $userGuid = $user->getGUID();

        $user->unload();

        $loadedUser = $userCollection->get($userGuid);

        $this->assertEquals(
            'Name LastName',
            $loadedUser->getValue('fullName'),
            'Ожидается, что значение для полного имени было высчитано автоматически'
        );

    }
}

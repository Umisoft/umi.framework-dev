<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\persister;

use umi\event\IEvent;
use umi\event\IEventObservant;
use umi\event\TEventObservant;
use umi\orm\collection\ICollectionFactory;
use umi\orm\object\IObject;
use umi\orm\persister\IObjectPersister;
use utest\orm\ORMDbTestCase;

/**
 * Тесты событий, поднимаемых до сохранения объектов
 */
class EventsTest extends ORMDbTestCase implements IEventObservant
{
    use TEventObservant;

    /**
     * {@inheritdoc}
     */
    protected function getCollectionConfig()
    {
        return [
            self::METADATA_DIR . '/mock/collections',
            [
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

    protected function setUpFixtures()
    {
        $this->subscribeTo($this->getObjectPersister());

        $this->bindEvent(
            IObjectPersister::EVENT_BEFORE_PERSISTING_NEW_OBJECT,
            function (IEvent $event) {
                /**
                 * @var IObject $user
                 */
                $user = $event->getParam('object');

                if (!$user->getValue('group')) {
                    $groupCollection = $this->getCollectionManager()->getCollection(self::USERS_GROUP);
                    $group = $groupCollection->add();
                    $group->setValue('name', $user->getValue('login') . 'Group');
                }
            },
            [self::USERS_USER]
        );

        $this->bindEvent(
            IObjectPersister::EVENT_BEFORE_PERSISTING_NEW_OBJECT,
            function (IEvent $event) {
                /**
                 * @var IObject $group
                 */
                $group = $event->getParam('object');
                $group->setValue('name', $group->getValue('name') . '1');

            },
            [self::USERS_GROUP]
        );
    }

    public function testEvents()
    {
        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $user = $userCollection->add();
        $user->setValue('login', 'Test');

        $this->getObjectPersister()->commit();

        $group = $this->getCollectionManager()->getCollection(self::USERS_GROUP)
            ->select()
            ->result()
            ->fetch();

        $this->assertEquals(
            'TestGroup1',
            $group->getValue('name'),
            'Ожидается, что добавление пользователя вызвало добавление группы и при добавлении группы ее имя было изменено'
        );
    }

}
 
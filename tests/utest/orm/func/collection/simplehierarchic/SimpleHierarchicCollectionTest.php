<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\collection\simplehierarchic;

use umi\orm\collection\ICollectionFactory;
use umi\orm\collection\SimpleHierarchicCollection;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IHierarchicObject;
use utest\orm\ORMDbTestCase;

class SimpleHierarchicCollectionTest extends ORMDbTestCase
{

    /**
     * @var SimpleHierarchicCollection $collection
     */
    private $collection;
    /**
     * @var IHierarchicObject $object объект коллекции
     */
    private $object;
    /**
     * @var IHierarchicObject $object2 объект коллекции
     */
    private $object2;
    /**
     * @var IHierarchicObject $wrongObject объект чужой коллекции
     */
    private $wrongObject;

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
                ],
                self::SYSTEM_MENU            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE_HIERARCHIC
                ]
            ],
            true
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setUpFixtures()
    {
        $this->collection = $this->getCollectionManager()->getCollection(self::SYSTEM_MENU);
        $this->object = $this->collection->add('menuItem1');
        $this->object2 = $this->collection->add('menuItem2', IObjectType::BASE, $this->object);
        $this->wrongObject = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG)->add('blog1');
    }

    public function testAdd()
    {

        $this->assertInstanceOf(
            'umi\orm\object\IHierarchicObject',
            $this->object,
            'Ожидается, что метод IHierarchicCollection::add() вернет IHierarchicObject'
        );
        $this->assertEquals(
            IObjectType::BASE,
            $this->object->getTypeName(),
            'Ожидается, что по умолчанию добавляется объект базового типа'
        );
        $this->assertNull($this->object->getParent(), 'Ожидается, что по умолчанию объект был добавлен у корень иерархии');

        $this->assertTrue(
            $this->object === $this->object2->getParent(),
            'Ожидается, что объект был созданным с заданным родителем'
        );
        $this->assertEquals(
            1,
            $this->object->getChildCount(),
            'Ожидается, что при добавлении объекта у его родителя возрастет количество детей'
        );
    }

    public function testImpossibleAdd()
    {
        $e = null;
        try {
            $this->collection->add('menuItem3', 'not_existing_type');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается, что нельзя добавить объект несуществующего типа'
        );

        $e = null;
        try {
            $this->collection->add('menuItem4', IObjectType::BASE, $this->wrongObject);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается, что при добавлении объекта нельзя выставить в качестве родителя объект другой коллекции'
        );
    }

    public function testContains()
    {
        $this->assertTrue(
            $this->collection->contains($this->object),
            'Ожидается, что коллекция содержит созданный в ней объект'
        );

        $this->assertFalse(
            $this->collection->contains($this->wrongObject),
            'Ожидается, что коллекция не содержит объект, коллекция которого не равна этой коллекции'
        );

    }

    public function testDelete()
    {
        $e = null;
        try {
            $this->collection->delete($this->wrongObject);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, исключение при попытке удалить из коллекции не принадлежащий ей объект'
        );

        $this->assertInstanceOf(
            'umi\orm\collection\IHierarchicCollection',
            $this->collection->delete($this->object2),
            'Ожидается, что метод ISimpleCollection::delete() вернет ISimpleCollection'
        );
        $this->assertEquals(
            0,
            $this->object->getChildCount(),
            'Ожидается, что при удалении объекта у его родителя уменьшится количество детей'
        );
    }

    public function testGetByUri()
    {
        $this->getObjectPersister()->commit();

        $this->assertInstanceOf(
            'umi\orm\object\IHierarchicObject',
            $this->collection->getByUri('/menuItem1/menuItem2'),
            'Ожидается, что метод IHierarchicCollection::getByUri() вернет IHierarchicObject'
        );

        $e = null;
        try {
            $this->collection->getByUri('/wrongUri');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается, исключение, если объект не был найден по URI'
        );

    }
}

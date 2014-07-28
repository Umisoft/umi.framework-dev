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
use umi\orm\collection\ISimpleHierarchicCollection;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IHierarchicObject;
use utest\orm\ORMDbTestCase;

/**
 * Тест перемещения по общей иерархии
 */
class SimpleHierarchicCollectionMoveTest extends ORMDbTestCase
{

    /**
     * @var IHierarchicObject $menuItem1
     */
    protected $menuItem1;
    /**
     * @var IHierarchicObject $menuItem2
     */
    protected $menuItem2;
    /**
     * @var IHierarchicObject $menuItem3
     */
    protected $menuItem3;
    /**
     * @var IHierarchicObject $menuItem4
     */
    protected $menuItem4;
    /**
     * @var IHierarchicObject $menuItem5
     */
    protected $menuItem5;
    /**
     * @var IHierarchicObject $menuItem6
     */
    protected $menuItem6;
    /**
     * @var IHierarchicObject $menuItem7
     */
    protected $menuItem7;
    /**
     * @var IHierarchicObject $menuItem8
     */
    protected $menuItem8;
    /**
     * @var ISimpleHierarchicCollection $menu
     */
    protected $menu;

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

    protected function setUpFixtures()
    {

        $this->menu = $this->getCollectionManager()->getCollection(self::SYSTEM_MENU);

        $this->menuItem1 = $this->menu->add('item1');
        $this->menuItem2 = $this->menu->add('item2');
        $this->menuItem3 = $this->menu->add('item3', IObjectType::BASE, $this->menuItem2);
        $this->menuItem4 = $this->menu->add('item4', IObjectType::BASE, $this->menuItem3);
        $this->menuItem5 = $this->menu->add('item5');
        $this->menuItem6 = $this->menu->add('item6', IObjectType::BASE, $this->menuItem5);
        $this->menuItem7 = $this->menu->add('item7', IObjectType::BASE, $this->menuItem6);
        $this->menuItem8 = $this->menu->add('item8', IObjectType::BASE, $this->menuItem5);

        $this->getObjectPersister()->commit();
    }

    public function testInitialHierarchyProperties()
    {

        $this->assertEquals(1, $this->menuItem1->getOrder());

        $this->assertEquals(2, $this->menuItem2->getOrder());
        $this->assertEquals('#2', $this->menuItem2->getMaterializedPath());
        $this->assertEquals(0, $this->menuItem2->getLevel());

        $this->assertEquals(1, $this->menuItem3->getOrder());

        $this->assertEquals('#2.3.4', $this->menuItem4->getMaterializedPath());
        $this->assertEquals(2, $this->menuItem4->getLevel());

        $this->assertEquals(3, $this->menuItem5->getOrder());

        $this->assertEquals(1, $this->menuItem6->getOrder());
        $this->assertEquals('#5.6', $this->menuItem6->getMaterializedPath());
        $this->assertEquals(1, $this->menuItem6->getLevel());
        $this->assertEquals(
            5,
            $this->menuItem6->getParent()
                ->getId()
        );

        $this->assertEquals('#5.6.7', $this->menuItem7->getMaterializedPath());
        $this->assertEquals('//item5/item6/item7', $this->menuItem7->getURI());
        $this->assertEquals(
            6,
            $this->menuItem7->getParent()
                ->getId()
        );
        $this->assertEquals(2, $this->menuItem7->getLevel());

    }

    public function testImpossibleMove()
    {
        $blog = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG)
            ->add('blog');
        $this->getObjectPersister()->commit();

        $e = null;
        try {
            $this->menu->move($blog);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что в простой иерархической коллекции невозможно переместить не принадлежащий ей объект'
        );

        $e = null;
        try {
            $this->menu->move($this->menuItem2, $blog);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что в простой иерархической коллекции невозможно переместить объект'
            . ' под не принадлежащий ей объект'
        );

        $e = null;
        try {
            $this->menu->move($this->menuItem2, $this->menuItem5, $blog);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что в простой иерархической коллекции невозможно переместить объект'
            . ' рядом с не принадлежащим ей объектом'
        );

    }

    public function testMoveFirstWithoutSwitchingTheBranch()
    {
        $this->menu->move($this->menuItem5);
        $this->getObjectPersister()->commit();

        $this->assertEquals(1, $this->menuItem5->getOrder());
        $this->assertEquals(2, $this->menuItem1->getOrder());
        $this->assertEquals(3, $this->menuItem2->getOrder());
    }

    public function testMoveAfterWithoutSwitchingTheBranch()
    {
        $this->menu->move($this->menuItem6, $this->menuItem5, $this->menuItem8);
        $this->getObjectPersister()->commit();

        $this->assertEquals(2, $this->menuItem8->getOrder());
        $this->assertEquals(3, $this->menuItem6->getOrder());
    }

    public function testMoveFirstWithSwitchingBranch()
    {
        $this->menu->move($this->menuItem6, $this->menuItem2);
        $this->getObjectPersister()->commit();

        $this->assertEquals(1, $this->menuItem6->getOrder());
        $this->assertEquals(2, $this->menuItem3->getOrder());

        $this->assertEquals(
            2,
            $this->menuItem6->getParent()
                ->getId()
        );

        $this->assertEquals('#2.6', $this->menuItem6->getMaterializedPath());
        $this->assertEquals('//item2/item6', $this->menuItem6->getURI());

        $this->assertEquals('#2.6.7', $this->menuItem7->getMaterializedPath());
        $this->assertEquals('//item2/item6/item7', $this->menuItem7->getURI());

        $this->assertEquals(1, $this->menuItem6->getLevel());
        $this->assertEquals(2, $this->menuItem7->getLevel());
    }

    public function testMoveAfterWithSwitchingBranch()
    {
        $this->menu->move($this->menuItem7, $this->menuItem2, $this->menuItem3);
        $this->getObjectPersister()->commit();

        $this->assertEquals(2, $this->menuItem7->getOrder());
        $this->assertEquals(
            2,
            $this->menuItem7->getParent()
                ->getId()
        );
        $this->assertEquals('#2.7', $this->menuItem7->getMaterializedPath());
        $this->assertEquals(1, $this->menuItem7->getLevel());
        $this->assertEquals('//item2/item7', $this->menuItem7->getURI());

    }

    public function testMoveFromRoot()
    {
        $this->menu->move($this->menuItem2, $this->menuItem7);
        $this->getObjectPersister()->commit();

        $this->assertEquals(1, $this->menuItem2->getOrder());
        $this->assertEquals(3, $this->menuItem2->getLevel());
        $this->assertEquals(
            7,
            $this->menuItem2->getParent()
                ->getId()
        );
        $this->assertEquals('#5.6.7.2', $this->menuItem2->getMaterializedPath());
        $this->assertEquals('//item5/item6/item7/item2', $this->menuItem2->getURI());

        $this->assertEquals('#5.6.7.2.3.4', $this->menuItem4->getMaterializedPath());
        $this->assertEquals('//item5/item6/item7/item2/item3/item4', $this->menuItem4->getURI());
        $this->assertEquals(5, $this->menuItem4->getLevel());

    }

    public function testMoveToRoot()
    {
        $this->menu->move($this->menuItem6, null);
        $this->getObjectPersister()->commit();

        $this->assertEquals(1, $this->menuItem6->getOrder());
        $this->assertEquals(0, $this->menuItem6->getLevel());
        $this->assertNull($this->menuItem6->getParent());
        $this->assertEquals('#6', $this->menuItem6->getMaterializedPath());
        $this->assertEquals('//item6', $this->menuItem6->getURI());

        $this->assertEquals('#6.7', $this->menuItem7->getMaterializedPath());
        $this->assertEquals('//item6/item7', $this->menuItem7->getURI());
        $this->assertEquals(1, $this->menuItem7->getLevel());
    }
}

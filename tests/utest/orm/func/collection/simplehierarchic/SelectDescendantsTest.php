<?php
/**
 * This file is part of UMI.CMS.
 *
 * @link http://umi-cms.ru
 * @copyright Copyright (c) 2007-2014 Umisoft ltd. (http://umisoft.ru)
 * @license For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace utest\orm\func\collection\simplehierarchic;

use umi\orm\collection\ICollectionFactory;
use umi\orm\collection\ISimpleHierarchicCollection;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IHierarchicObject;
use umi\orm\selector\ISelector;
use utest\orm\ORMDbTestCase;

/**
 * Тесты выборки потомков
 */
class SelectDescendantsTest extends ORMDbTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionConfig()
    {
        return [
            self::METADATA_DIR . '/mock/collections',
            [
                self::SYSTEM_MENU            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE_HIERARCHIC
                ]
            ],
            true
        ];
    }

    /**
     * @var ISimpleHierarchicCollection $menu
     */
    protected $menu;

    protected $guid1;
    protected $guid2;

    /**
     * {@inheritdoc}
     */
    protected function setUpFixtures()
    {
        $this->menu = $this->getCollectionManager()->getCollection(self::SYSTEM_MENU);

        $item1 = $this->menu->add('4');
        $this->guid1 = $item1->getGUID();
        $item2 = $this->menu->add('3', IObjectType::BASE, $item1);
        $this->guid2 = $item2->getGUID();

        $this->menu->add('2', IObjectType::BASE, $item2);
        $this->menu->add('1', IObjectType::BASE, $item2);

        $this->getObjectPersister()->commit();
        $this->getObjectManager()->unloadObjects();
    }

    public function testDefaultSelectDescendants()
    {
        $result = $this->menu->selectDescendants();
        $this->assertEquals(4, $result->getTotal());
        $this->assertEquals(4, $result[0]->getSlug());
        $this->assertEquals(3, $result[1]->getSlug());
        $this->assertEquals(2, $result[2]->getSlug());
        $this->assertEquals(1, $result[3]->getSlug());

        $result = $this->menu->selectDescendants($this->menu->get($this->guid1));
        $this->assertEquals(3, $result->getTotal());
        $this->assertEquals(3, $result[0]->getSlug());
        $this->assertEquals(2, $result[1]->getSlug());
        $this->assertEquals(1, $result[2]->getSlug());

        $result = $this->menu->selectDescendants($this->menu->get($this->guid1), 1);
        $this->assertEquals(1, $result->getTotal());
        $this->assertEquals(3, $result[0]->getSlug());
    }

    public function testSelectDescendantsDesc()
    {
        $result = $this->menu->selectDescendants(null, null, IHierarchicObject::FIELD_ORDER, ISelector::ORDER_DESC);
        $this->assertEquals(4, $result->getTotal());
        $this->assertEquals(4, $result[0]->getSlug());
        $this->assertEquals(3, $result[1]->getSlug());
        $this->assertEquals(1, $result[2]->getSlug());
        $this->assertEquals(2, $result[3]->getSlug());
    }

    public function testSelectDescendantsOrderedBySlug()
    {
        $result = $this->menu->selectDescendants(null, null, IHierarchicObject::FIELD_SLUG);
        $this->assertEquals(4, $result->getTotal());
        $this->assertEquals(4, $result[0]->getSlug());
        $this->assertEquals(3, $result[1]->getSlug());
        $this->assertEquals(1, $result[2]->getSlug());
        $this->assertEquals(2, $result[3]->getSlug());
    }

    public function testDefaultSelectChildren()
    {
        $result = $this->menu->selectChildren();
        $this->assertEquals(1, $result->getTotal());
        $this->assertEquals(4, $result[0]->getSlug());

        $result = $this->menu->selectChildren($this->menu->get($this->guid2));
        $this->assertEquals(2, $result->getTotal());
        $this->assertEquals(2, $result[0]->getSlug());
        $this->assertEquals(1, $result[1]->getSlug());

    }

    public function testSelectChildrenDesc()
    {
        $result = $this->menu->selectChildren($this->menu->get($this->guid2), IHierarchicObject::FIELD_ORDER, ISelector::ORDER_DESC);
        $this->assertEquals(2, $result->getTotal());
        $this->assertEquals(1, $result[0]->getSlug());
        $this->assertEquals(2, $result[1]->getSlug());
    }

    public function testSelectChildrenOrderedBySlug()
    {
        $result = $this->menu->selectChildren($this->menu->get($this->guid2), IHierarchicObject::FIELD_SLUG);
        $this->assertEquals(2, $result->getTotal());
        $this->assertEquals(1, $result[0]->getSlug());
        $this->assertEquals(2, $result[1]->getSlug());
    }
}
 
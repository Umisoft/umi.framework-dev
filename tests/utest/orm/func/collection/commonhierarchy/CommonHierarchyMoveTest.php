<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

use umi\orm\collection\ICollectionFactory;
use umi\orm\collection\ICommonHierarchy;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IHierarchicObject;
use umi\orm\object\IObject;
use utest\orm\ORMDbTestCase;

/**
 * Тест перемещения по общей иерархии
 */
class CommonHierarchyMoveTest extends ORMDbTestCase
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

    /**
     * @var IHierarchicObject $blog1
     */
    protected $blog1;
    /**
     * @var IHierarchicObject $blog2
     */
    protected $blog2;
    /**
     * @var IHierarchicObject $blog3
     */
    protected $blog3;
    /**
     * @var IHierarchicObject $blog4
     */
    protected $blog4;
    /**
     * @var IHierarchicObject $blog5
     */
    protected $blog5;
    /**
     * @var IHierarchicObject $post1
     */
    protected $post1;
    /**
     * @var IHierarchicObject $post2
     */
    protected $post2;
    /**
     * @var IHierarchicObject $post3
     */
    protected $post3;
    /**
     * @var ICommonHierarchy $hierarchy
     */
    protected $hierarchy;

    protected function setUpFixtures()
    {

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $postsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_POST);
        $this->hierarchy = $this->getCollectionManager()->getCollection(self::SYSTEM_HIERARCHY);

        $this->blog1 = $blogsCollection->add('blog1');
        $this->blog1->setValue('title', 'blog');

        $this->blog2 = $blogsCollection->add('blog2', IObjectType::BASE, $this->blog1);
        $this->blog2->setValue('title', 'blog2');

        $this->post1 = $postsCollection->add('post1', IObjectType::BASE, $this->blog1);
        $this->post1->setValue('title', 'post1');

        $this->blog3 = $blogsCollection->add('blog3', IObjectType::BASE, $this->blog1);
        $this->blog3->setValue('title', 'blog3');

        $this->post2 = $postsCollection->add('post2', IObjectType::BASE, $this->blog3);
        $this->post2->setValue('title', 'post2');

        $this->post3 = $postsCollection->add('post3', IObjectType::BASE, $this->post2);
        $this->post3->setValue('title', 'post3');

        $this->blog4 = $blogsCollection->add('blog2', IObjectType::BASE, $this->post3);
        $this->blog4->setValue('title', 'blog4');

        $this->blog5 = $blogsCollection->add('blog5');
        $this->blog5->setValue('title', 'blog5');

        $this->getObjectPersister()->commit();

        $this->resetQueries();
    }

    public function testInitialHierarchyProperties()
    {

        $this->assertEquals(1, $this->blog2->getOrder());
        $this->assertEquals(2, $this->post1->getOrder());
        $this->assertEquals(3, $this->blog3->getOrder());

        $this->assertEquals('#1.4.5', $this->post2->getMaterializedPath());
        $this->assertEquals('#1.4.5.6', $this->post3->getMaterializedPath());

        $this->assertEquals(2, $this->post2->getLevel());
        $this->assertEquals(3, $this->post3->getLevel());
        $this->assertEquals(0, $this->blog1->getLevel());

        $this->assertEquals('//blog1/blog2', $this->blog2->getURI());
        $this->assertEquals('//blog1/blog3/post2', $this->post2->getURI());
        $this->assertEquals('//blog1/blog3/post2/post3', $this->post3->getURI());

    }

    public function testOutOfDateMove()
    {
        $this->blog3->setVersion(10);
        $e = null;
        try {
            $this->hierarchy->move($this->blog3, $this->post1);
            $this->getObjectPersister()->commit();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что невозможно переместить объект, если его версия была ранее изменена'
        );
    }

    public function testImpossibleMove()
    {
        $e = null;
        try {
            $this->hierarchy->move($this->blog3, $this->post3);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что невозможно переместить объект под своего ребенка'
        );

        $e = null;
        try {
            $this->hierarchy->move($this->blog3, $this->blog1, $this->post3);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что невозможно переместить объект после объекта, находящегося не в ветке перемещения'
        );

        $e = null;
        try {
            $this->hierarchy->move($this->blog4, $this->blog1);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что невозможно переместить объект, если его новый итоговый урл совпадает с уже существующим'
        );

        $this->blog4->setValue('title', 'new_title');
        $e = null;
        try {
            $this->hierarchy->move($this->blog4, $this->blog1);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что невозможно переместить объект и изменить объекты за одну транзакцию'
        );
    }

    public function testMoveFirstWithoutSwitchingTheBranch()
    {
        $this->hierarchy->move($this->blog3, $this->blog1);
        $this->getObjectPersister()->commit();

        $this->assertEquals(2, $this->blog2->getOrder());
        $this->assertEquals(3, $this->post1->getOrder());
        $this->assertEquals(1, $this->blog3->getOrder());
    }

    public function testMoveAfterWithoutSwitchingTheBranch()
    {
        $this->hierarchy->move($this->blog3, $this->blog1, $this->blog2);
        $this->getObjectPersister()->commit();

        $this->assertEquals(1, $this->blog2->getOrder());
        $this->assertEquals(3, $this->post1->getOrder());
        $this->assertEquals(2, $this->blog3->getOrder());

    }

    public function testMoveFirstWithSwitchingBranch()
    {
        $this->hierarchy->move($this->post2, $this->blog1);
        $this->getObjectPersister()->commit();

        $this->assertEquals(1, $this->post2->getOrder());
        $this->assertEquals(
            1,
            $this->post2->getParent()
                ->getId()
        );
        $this->assertEquals('#1.5', $this->post2->getMaterializedPath());
        $this->assertEquals('#1.5.6', $this->post3->getMaterializedPath());
        $this->assertEquals(1, $this->post2->getLevel());
        $this->assertEquals(2, $this->post3->getLevel());

        $this->assertEquals('//blog1/post2', $this->post2->getURI());
        $this->assertEquals('//blog1/post2/post3', $this->post3->getURI());

    }

    public function testMoveAfterWithSwitchingBranch()
    {
        $this->hierarchy->move($this->blog2, $this->post2, $this->post3);
        $this->getObjectPersister()->commit();

        $this->assertEquals(2, $this->blog2->getOrder());
        $this->assertEquals(
            5,
            $this->blog2->getParent()
                ->getId()
        );
        $this->assertEquals('#1.4.5.2', $this->blog2->getMaterializedPath());
        $this->assertEquals('//blog1/blog3/post2/blog2', $this->blog2->getURI());

    }

    public function testMoveFromRoot()
    {
        $this->hierarchy->move($this->blog1, $this->blog5);
        $this->getObjectPersister()->commit();

        $this->assertEquals(1, $this->blog1->getOrder());
        $this->assertEquals(1, $this->blog1->getLevel());
        $this->assertEquals(
            8,
            $this->blog1->getParent()
                ->getId()
        );
        $this->assertEquals('#8.1', $this->blog1->getMaterializedPath());
        $this->assertEquals('//blog5/blog1', $this->blog1->getURI());

        $this->assertEquals('#8.1.4.5.6', $this->post3->getMaterializedPath());
        $this->assertEquals('//blog5/blog1/blog3/post2/post3', $this->post3->getURI());

    }

    public function testMoveFirstToRoot()
    {
        $this->hierarchy->move($this->blog3, null);
        $this->getObjectPersister()->commit();

        $this->assertEquals(1, $this->blog3->getOrder());
        $this->assertEquals(0, $this->blog3->getLevel());
        $this->assertNull($this->blog3->getParent());
        $this->assertEquals('#4', $this->blog3->getMaterializedPath());
        $this->assertEquals('//blog3', $this->blog3->getURI());

        $this->assertEquals('#4.5.6', $this->post3->getMaterializedPath());
        $this->assertEquals(2, $this->post3->getLevel());
    }

    public function testMoveAfterToRoot()
    {
        $this->hierarchy->move($this->blog3, null, $this->blog1);
        $this->getObjectPersister()->commit();

        $this->assertEquals(2, $this->blog3->getOrder());
        $this->assertEquals(3, $this->blog5->getOrder());
    }
}

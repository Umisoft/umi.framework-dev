<?php
/**
 * This file is part of UMI.CMS.
 *
 * @link http://umi-cms.ru
 * @copyright Copyright (c) 2007-2014 Umisoft ltd. (http://umisoft.ru)
 * @license For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace utest\orm\func\object\property;

use umi\orm\collection\ICollectionFactory;
use umi\orm\metadata\IObjectType;
use utest\orm\ORMDbTestCase;

/**
 * Тест слагов.
 */
class AutogeneratedSlugTest extends ORMDbTestCase
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

    public function testHierarchicObject()
    {

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);

        $blog1 = $blogsCollection->add();
        $blog2 = $blogsCollection->add(null, IObjectType::BASE, $blog1);

        $this->getObjectPersister()->commit();

        $this->assertEquals('//1', $blog1->getURI());
        $this->assertEquals('/1', $blog1->getURl());
        $this->assertEquals('1', $blog1->getSlug());

        $this->assertEquals('//1/2', $blog2->getURI());
        $this->assertEquals('/1/2', $blog2->getURL());
        $this->assertEquals('2', $blog2->getSlug());

    }
}
 
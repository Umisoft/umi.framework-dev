<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\rss\unit;

use DateTime;
use umi\rss\RssFeed;
use umi\rss\RssItem;
use utest\rss\RssTestCase;

/**
 * Тесты события
 *
 */
class RssTest extends RssTestCase
{
    public function testFeed()
    {
        $feed = new RssFeed('http://example.com/', 'title', 'description');
        $this->resolveOptionalDependencies($feed);

        $dateTime = new DateTime();
        $dateTime2 = new DateTime();

        $feed->addItem('http://example.com/item/1', 'title item 1', 'content item 1', $dateTime);
        $feed->addItem('http://example.com/item/2', 'title item 2', 'content item 2', $dateTime2);

        $this->assertEquals($feed->getUrl(), 'http://example.com/');
        $this->assertEquals($feed->getTitle(), 'title');
        $this->assertEquals($feed->getDescription(), 'description');

        $feed->setUrl('http://new.example.com/');
        $feed->setTitle('title new');
        $feed->setDescription('description new');

        $this->assertEquals($feed->getUrl(), 'http://new.example.com/');
        $this->assertEquals($feed->getTitle(), 'title new');
        $this->assertEquals($feed->getDescription(), 'description new');

        $items = $feed->getRssItems();
        $this->assertCount(2, $items);

        $this->assertEquals($items[0]->getUrl(), 'http://example.com/item/1');
        $this->assertEquals($items[0]->getTitle(), 'title item 1');
        $this->assertEquals($items[0]->getContent(), 'content item 1');
        $this->assertSame($items[0]->getDate(),$dateTime);

        $this->assertEquals($items[1]->getUrl(), 'http://example.com/item/2');
        $this->assertEquals($items[1]->getTitle(), 'title item 2');
        $this->assertEquals($items[1]->getContent(), 'content item 2');
        $this->assertSame($items[1]->getDate(),$dateTime2);
    }

    public function testItem()
    {
        $dateTime = new DateTime();
        $dateTime2 = new DateTime();

        $rssItem = new RssItem('http://example.com/', 'title', 'content', $dateTime);

        $this->assertEquals($rssItem->getUrl(), 'http://example.com/');
        $this->assertEquals($rssItem->getTitle(), 'title');
        $this->assertEquals($rssItem->getContent(), 'content');
        $this->assertSame($rssItem->getDate(), $dateTime);

        $rssItem->setTitle('title 2');
        $rssItem->setContent('content 2');
        $rssItem->setUrl('http://example.com/2');
        $rssItem->setDate($dateTime2);

        $this->assertEquals($rssItem->getUrl(), 'http://example.com/2');
        $this->assertEquals($rssItem->getTitle(), 'title 2');
        $this->assertEquals($rssItem->getContent(), 'content 2');
        $this->assertSame($rssItem->getDate(), $dateTime2);
    }

}



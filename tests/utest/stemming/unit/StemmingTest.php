<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\session\stemming\unit;

use umi\stemming\IStemming;
use utest\TestCase;

/**
 * Набор тестов для инструментов штемминга.
 */
class StemmingTest extends TestCase
{
    public function setUpFixtures()
    {
        $this->getTestToolkit()
            ->registerToolbox(require LIBRARY_PATH . '/stemming/toolbox/config.php');
    }

    /**
     * @param $type
     * @return array
     */
    protected function getWordFixtures($type)
    {
        /** @noinspection PhpIncludeInspection */
        return require __DIR__ . "/../fixtures/$type.php";
    }

    /**
     * @return IStemming
     */
    protected function getStemming()
    {
        return $this->getTestToolkit()
            ->getService('umi\stemming\IStemming');
    }

    public function testService()
    {
        /** @var $stemming IStemming */
        $stemming = $this->getTestToolkit()
            ->getService('umi\stemming\IStemming');
        $this->assertInstanceOf(
            'umi\stemming\IStemming',
            $stemming,
            'Stemming service must be registered'
        );
    }

    /**
     * @dataProvider provideBases
     * @param $input
     * @param $variants
     */
    public function testGetBaseForm($input, $variants)
    {
        $this->assertEquals(
            $variants,
            $this->getStemming()
                ->getBaseForm($input),
            'Word base must be detected correctly'
        );
    }

    /**
     * @dataProvider provideAllForms
     * @param $input
     * @param $variants
     */
    public function testGetAllForms($input, $variants)
    {
        $this->assertEquals(
            $variants,
            $this->getStemming()
                ->getAllForms($input),
            'Word forms must be detected correctly'
        );
    }

    /**
     * @dataProvider providePartsOfSpeech
     * @param $input
     * @param $variants
     */
    public function testGetPartsOfSpeech($input, $variants)
    {
        $this->assertEquals(
            $variants,
            $this->getStemming()
                ->getPartOfSpeech($input),
            'Part of speech must be detected correctly'
        );
    }

    /**
     * @dataProvider provideCommonRoots
     * @param $input
     * @param $expect
     */
    public function testGetCommonRoot($input, $expect)
    {
        $root = $this->getStemming()
            ->getCommonRoot($input);
        $this->assertEquals(
            $expect,
            $root,
            'Common root must be found correctly'
        );
    }

    /**
     * @dataProvider provideSearchableRoots
     */
    public function testGetSearchableRoot($input, $expect)
    {
        $root = $this->getStemming()
            ->getSearchableRoot($input, 3);
        $this->assertEquals($expect, $root, 'Searchable root must be found correctly');
    }

    public function provideBases()
    {
        return $this->getWordFixtures('base');
    }

    public function provideAllForms()
    {
        return $this->getWordFixtures('all');
    }

    public function providePartsOfSpeech()
    {
        return $this->getWordFixtures('parts-of-speech');
    }

    public function provideCommonRoots()
    {
        return $this->getWordFixtures('roots');
    }

    public function provideSearchableRoots()
    {
        return $this->getWordFixtures('roots-searchable');
    }
}

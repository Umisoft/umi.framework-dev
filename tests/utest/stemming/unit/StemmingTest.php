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
 * Class StemmingTest
 */
class StemmingTest extends TestCase
{
    public function setUpFixtures()
    {
        $this->getTestToolkit()
            ->registerToolbox(require LIBRARY_PATH . '/stemming/toolbox/config.php');
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

    public function testBaseForms()
    {
        foreach ($this->getWordFixtures('base') as $input => $variants) {
            $this->assertEquals(
                $variants,
                $this->getStemming()
                    ->getBaseForm($input),
                'Word base must be detected correctly'
            );
        }
    }

    public function testAllForms()
    {
        foreach ($this->getWordFixtures('all') as $input => $variants) {
            $this->assertEquals(
                $variants,
                $this->getStemming()
                    ->getAllForms($input),
                'Word forms must be detected correctly'
            );
        }
    }

    public function testPartsOfSpeech()
    {
        foreach ($this->getWordFixtures('parts-of-speech') as $input => $variants) {
            $this->assertEquals(
                $variants,
                $this->getStemming()
                    ->getPartOfSpeech($input),
                'Part of speech must be detected correctly'
            );
        }
    }

    public function testParadigms()
    {
        foreach ($this->getWordFixtures('base') as $input => $variants) {
            $paradigms = $this->getStemming()
                ->getWordParadigms($input);
            $this->assertInstanceOf(
                'phpMorphy_Paradigm_Collection',
                $paradigms,
                'Words paradigms must be produced'
            );
            $this->assertGreaterThan(0, $paradigms->count(), 'Paradigms must be filled');
        }
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
}

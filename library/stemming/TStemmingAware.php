<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace umi\stemming;

/**
 * Class TStemmingAware
 */
trait TStemmingAware
{
    /**
     * @var IStemming $traitStemming
     */
    private $traitStemming;

    /**
     * Внедряет сервис штемминга
     * @param IStemming $stemmingService
     * @return void
     */
    public function setStemming(IStemming $stemmingService)
    {
        $this->traitStemming = $stemmingService;
    }

    /**
     * @throws \UnexpectedValueException
     * @return \umi\stemming\IStemming
     */
    public function getStemming()
    {
        if (is_null($this->traitStemming)) {
            throw new \UnexpectedValueException("Stemming service is not injected");
        }
        return $this->traitStemming;
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata\field\relation;

use umi\orm\metadata\field\IField;
use umi\orm\metadata\field\relation\BelongsToSelfRelationField;
use utest\orm\unit\metadata\field\FieldTestCase;

/**
 * Тест поля хранителя связи.
 */
class BelongsToSelfRelationFieldTest extends FieldTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getField()
    {
        return new BelongsToSelfRelationField(
            'mock',
            IField::TYPE_BELONGS_TO,
            ['collectionName' => 'targetCollection']
        );
    }

    public function testConfig()
    {
        $e = null;
        try {
            new BelongsToSelfRelationField('mock', IField::TYPE_BELONGS_TO);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать поле BelongsToSelfRelation без указания целевой коллекции'
        );

        $config = ['collectionName' => 'targetCollection'];
        $field1 = new BelongsToSelfRelationField('mock', IField::TYPE_BELONGS_TO, $config);

        $this->assertEquals('targetCollection', $field1->getTargetCollectionName(), 'Неверно прочитан конфиг');
    }

    public function testMethods()
    {
        $field = new BelongsToSelfRelationField(
            'mock',
            IField::TYPE_BELONGS_TO,
            ['collectionName' => 'targetCollection']
        );

        $this->assertEquals(
            'integer',
            $field->getDataType(),
            'Ожидается, что тип данных у поля хранителя связи - integer'
        );
    }
}

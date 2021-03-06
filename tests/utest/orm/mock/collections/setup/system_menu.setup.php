<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use umi\orm\metadata\ICollectionDataSource;

return function (ICollectionDataSource $dataSource) {

    $masterServer = $dataSource->getMasterServer();
    $schemaManager = $masterServer
        ->getConnection()
        ->getSchemaManager();
    $tableScheme = new Table($dataSource->getSourceName());

    $tableScheme->addOption('engine', 'InnoDB');

    $tableScheme
        ->addColumn('id', Type::INTEGER)
        ->setAutoincrement(true);
    $tableScheme
        ->addColumn('guid', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('type', Type::STRING)
        ->setNotnull(false);

    $tableScheme
        ->addColumn(
            'version',
            Type::INTEGER
        )
        ->setUnsigned(true)
        ->setDefault(1);

    $tableScheme
        ->addColumn('pid', Type::INTEGER)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('mpath', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('uri', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('slug', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('level', Type::INTEGER)
        ->setUnsigned(true)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('order', Type::INTEGER)
        ->setUnsigned(true)
        ->setNotnull(false);

    $tableScheme
        ->addColumn('title', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('title_en', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('link', Type::STRING)
        ->setNotnull(false);

    $tableScheme->setPrimaryKey(['id']);

    $tableScheme->addUniqueIndex(['guid'], 'menu_guid');
    $tableScheme->addIndex(['pid'], 'menu_parent');

    $tableScheme->addUniqueIndex(['mpath'], 'menu_mpath');
    $tableScheme->addIndex(['type'], 'menu_type');

    /** @noinspection PhpParamsInspection */
    $tableScheme->addForeignKeyConstraint(
        $tableScheme->getName(),
        ['pid'],
        ['id'],
        ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'],
        'FK_menu_parent'
    );
    return $schemaManager->getDatabasePlatform()->getCreateTableSQL(
        $tableScheme,
        AbstractPlatform::CREATE_INDEXES | AbstractPlatform::CREATE_FOREIGNKEYS
    );
};

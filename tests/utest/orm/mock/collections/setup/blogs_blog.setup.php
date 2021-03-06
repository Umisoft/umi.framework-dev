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
    $table = new Table($dataSource->getSourceName());

    $table->addOption('engine', 'InnoDB');

    $table
        ->addColumn('id', Type::INTEGER)
        ->setAutoincrement(true);
    $table
        ->addColumn('guid', Type::GUID)
        ->setNotnull(false);
    $table
        ->addColumn('type', Type::STRING)
        ->setNotnull(false);
    $table
        ->addColumn('version', Type::INTEGER)
        ->setUnsigned(true)
        ->setDefault(1);

    $table
        ->addColumn('pid', Type::INTEGER)
        ->setNotnull(false);
    $table
        ->addColumn('mpath', Type::STRING)
        ->setNotnull(false);
    $table
        ->addColumn('uri', Type::STRING)
        ->setNotnull(false);
    $table
        ->addColumn('slug', Type::STRING)
        ->setNotnull(false);
    $table
        ->addColumn('level', Type::INTEGER)
        ->setUnsigned(true)
        ->setNotnull(false);
    $table
        ->addColumn('order', Type::INTEGER)
        ->setUnsigned(true)
        ->setNotnull(false);

    $table
        ->addColumn('publish_time', Type::DATE)
        ->setNotnull(false);
    $table
        ->addColumn('title', Type::STRING)
        ->setNotnull(false);
    $table
        ->addColumn('title_en', Type::STRING)
        ->setNotnull(false);
    $table
        ->addColumn('title_gb', Type::STRING)
        ->setNotnull(false);
    $table
        ->addColumn('title_ua', Type::STRING)
        ->setNotnull(false);
    $table
        ->addColumn('owner_id', Type::INTEGER)
        ->setNotnull(false);

    $table->setPrimaryKey(['id']);
    $table->addUniqueIndex(['guid'], 'blog_guid');
    $table->addIndex(['pid'], 'blog_parent');
    $table->addUniqueIndex(['pid', 'slug'], 'blog_pid_slug');

    $table->addUniqueIndex(['mpath'], 'blog_mpath');
    $table->addUniqueIndex(['uri'], 'blog_uri');
    $table->addIndex(['type'], 'blog_type');

    $table->addIndex(['owner_id'], 'blog_owner');

    /** @noinspection PhpParamsInspection */
    $table->addForeignKeyConstraint(
        'umi_mock_hierarchy',
        ['pid'],
        ['id'],
        ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'],
        'FK_blog_parent'
    );

    /** @noinspection PhpParamsInspection */
    $table->addForeignKeyConstraint(
        'umi_mock_users',
        ['owner_id'],
        ['id'],
        ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'],
        'FK_blog_owner'
    );

    return $schemaManager->getDatabasePlatform()->getCreateTableSQL(
        $table,
        AbstractPlatform::CREATE_INDEXES | AbstractPlatform::CREATE_FOREIGNKEYS
    );
};

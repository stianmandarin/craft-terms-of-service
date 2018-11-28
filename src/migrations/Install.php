<?php
/**
 * Craft Terms of Service plugin for Craft CMS 3.x
 *
 * Force logged in users to accept the sites TOS before being able to use the site.
 *
 * @link      https://mandarindesign.no
 * @copyright Copyright (c) 2018 Mandarin Design
 */

namespace mandarindesign\crafttermsofservice\migrations;

use mandarindesign\crafttermsofservice\CraftTermsOfService;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * Craft Terms of Service Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Mandarin Design
 * @package   CraftTermsOfService
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        // crafttermsofservice_crafttermsofservicerecord table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%crafttermsofservice_crafttermsofservicerecord}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%crafttermsofservice_crafttermsofservicerecord}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    // Custom columns in the table
                    // 'siteId' => $this->integer()->notNull(),
                    'userId' => $this->integer()->notNull(),
                    'userAcceptedVersion' => $this->integer()->notNull(),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
        // crafttermsofservice_crafttermsofservicerecord table
        $this->createIndex(
            $this->db->getIndexName(
                '{{%crafttermsofservice_crafttermsofservicerecord}}',
                'userId',
                true
            ),
            '{{%crafttermsofservice_crafttermsofservicerecord}}',
            'userId',
            true
        );
        $this->createIndex(
            $this->db->getIndexName(
                '{{%crafttermsofservice_crafttermsofservicerecord}}',
                'userAcceptedVersion',
                false
            ),
            '{{%crafttermsofservice_crafttermsofservicerecord}}',
            'userAcceptedVersion',
            false
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
        // crafttermsofservice_crafttermsofservicerecord table
        /*$this->addForeignKey(
            $this->db->getForeignKeyName('{{%crafttermsofservice_crafttermsofservicerecord}}', 'siteId'),
            '{{%crafttermsofservice_crafttermsofservicerecord}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );*/
    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
        // crafttermsofservice_crafttermsofservicerecord table
        $this->dropTableIfExists('{{%crafttermsofservice_crafttermsofservicerecord}}');
    }
}

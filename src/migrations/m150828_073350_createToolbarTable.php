<?php
/**
 * m150828_073350_createToolbarTable.php
 *
 * PHP version 5.3+
 *
 * @author    David Ghyse <dghyse@ibitux.com>
 * @copyright 2010-2015 Ibitux
 * @license   http://www.ibitux.com/license license
 * @version   3.0.1
 * @link      http://code.ibitux.net/projects/ibitux-2013
 * @category  migrations
 * @package   application.migrations
 */

class m150828_073350_createToolbarTable extends CDbMigration
{
	public function up()
	{
		$this->createTable(
			'{{toolbars}}',
			[
				'toolbarId' => 'pk',
				'toolbarName' => 'string not null',
				'toolbarCore' => 'text default null',
				'toolbarTextDescription' => 'text default null',
				'toolbarIsActive' => 'boolean default 0',
				'toolbarDateCreate' => 'datetime default null',
				'toolbarDateUpdate' => 'datetime default null',

			],
			'ENGINE=InnoDB DEFAULT CHARSET=utf8, AUTO_INCREMENT = 1'
		);
		return true;
	}

	public function down()
	{
		$this->dropTable('{{toolbars}}');
		return true;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
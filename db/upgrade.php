<?php
/**
 * Created by Vojtěch Havel on 2014/12/13
 */

function xmldb_block_overachiever_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2014121303) {

        // Define table block_oa_points to be created.
        $table = new xmldb_table('block_oa_points');

        // Adding fields to table block_oa_points.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('points', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('user', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table block_oa_points.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('user'), 'mdl_user', array('id'));

        // Conditionally launch create table for block_oa_points.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Overachiever savepoint reached.
        upgrade_block_savepoint(true, 2014121303, 'overachiever');
    }
}
<?php
/**
 * Created by Vojtěch Havel on 2014/12/13
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_block_overachiever_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2014121303) {

        // Define table block_oa_users to be created.
        $table = new xmldb_table('block_oa_users');

        // Adding fields to table block_oa_users.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('points', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('user', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table block_oa_users.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('user', XMLDB_KEY_FOREIGN_UNIQUE, array('user'), 'mdl_user', array('id'));

        // Conditionally launch create table for block_oa_users.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Overachiever savepoint reached.
        upgrade_block_savepoint(true, 2014121303, 'overachiever');
    }
    if ($oldversion < 2014121308) {

        // Define table block_oa_ponits to be renamed to block_oa_users.
        $table = new xmldb_table('block_oa_points');

        // Launch rename table for block_oa_ponits.
        $dbman->rename_table($table, 'block_oa_users');

        // Overachiever savepoint reached.
        upgrade_block_savepoint(true, 2014121308, 'overachiever');
    }
    if ($oldversion < 2014121309) {

        // Define field streak to be added to block_oa_users.
        $table = new xmldb_table('block_oa_users');
        $field = new xmldb_field('streak', XMLDB_TYPE_INTEGER, '5', null, null, null, null, 'user');

        // Conditionally launch add field streak.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field qanswered to be added to block_oa_users.
        $field = new xmldb_field('qanswered', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'streak');

        // Conditionally launch add field qanswered.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field qcorrect to be added to block_oa_users.
        $field = new xmldb_field('qcorrect', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'qanswered');

        // Conditionally launch add field qcorrect.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Overachiever savepoint reached.
        upgrade_block_savepoint(true, 2014121309, 'overachiever');
    }
    if ($oldversion < 2014121310) {

        // Define table block_oa_survived to be created.
        $table = new xmldb_table('block_oa_survived');

        // Adding fields to table block_oa_survived.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('question', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table block_oa_survived.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_oa_survived.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Overachiever savepoint reached.
        upgrade_block_savepoint(true, 2014121310, 'overachiever');
    }

    if ($oldversion < 2014122901) {

        // Define table block_oa_current to be created.
        $table = new xmldb_table('block_oa_current');

        // Adding fields to table block_oa_current.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('question', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table block_oa_current.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_oa_current.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Overachiever savepoint reached.
        upgrade_block_savepoint(true, 2014122901, 'overachiever');
    }

    if ($oldversion < 2014123000) {

        // Define field streak to be dropped from block_oa_users.
        $table = new xmldb_table('block_oa_users');
        $field = new xmldb_field('streak');

        // Conditionally launch drop field streak.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define table block_oa_streak to be created.
        $table = new xmldb_table('block_oa_streak');

        // Adding fields to table block_oa_streak.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('streak', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table block_oa_streak.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_oa_streak.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Overachiever savepoint reached.
        upgrade_block_savepoint(true, 2014123000, 'overachiever');
    }

    if ($oldversion < 2015010404) {

        // Define table block_oa_questions to be created.
        $table = new xmldb_table('block_oa_questions');

        // Adding fields to table block_oa_questions.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('qid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table block_oa_questions.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_oa_questions.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Overachiever savepoint reached.
        upgrade_block_savepoint(true, 2015010404, 'overachiever');
    }

    if ($oldversion < 2015030600) {

        // Define table block_oa_badges to be created.
        $table = new xmldb_table('block_oa_badges');

        // Adding fields to table block_oa_badges.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('type', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, null);
        $table->add_field('param', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('badgeid', XMLDB_TYPE_INTEGER, '20', null, null, null, null);

        // Adding keys to table block_oa_badges.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_oa_badges.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Overachiever savepoint reached.
        upgrade_block_savepoint(true, 2015030600, 'overachiever');
    }





    return true;
}
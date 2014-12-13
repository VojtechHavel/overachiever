<?php
/**
 * Created by Vojtěch Havel on 2014/12/12
 */

    $capabilities = array(

        'block/overachiever:myaddinstance' => array(
            'captype' => 'write',
            'contextlevel' => CONTEXT_SYSTEM,
            'archetypes' => array(
                'user' => CAP_ALLOW
            ),

            'clonepermissionsfrom' => 'moodle/my:manageblocks'
        ),

        'block/overachiever:addinstance' => array(
            'riskbitmask' => RISK_SPAM | RISK_XSS,

            'captype' => 'write',
            'contextlevel' => CONTEXT_BLOCK,
            'archetypes' => array(
                'editingteacher' => CAP_ALLOW,
                'manager' => CAP_ALLOW
            ),

            'clonepermissionsfrom' => 'moodle/site:manageblocks'
        ),
    );
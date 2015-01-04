<?php
/**
 * Created by Vojtěch Havel on 2014/12/14
 */
defined('MOODLE_INTERNAL') || die();
/**
 * alternative to question_preview_url from questionlib
 * with arbitrary path as last parameter - default: '/question/preview.php'
 * @param null $preferredbehaviour
 * @param null $maxmark
 * @param null $displayoptions
 * @param null $variant
 * @param null $context
 * @param string $urlpath
 * @return moodle_url
 */
function question_general_url($preferredbehaviour = null,
                              $maxmark = null, $displayoptions = null, $variant = null, $context = null, $urlpath = '/question/preview.php') {
    if (is_null($context)) {
        global $PAGE;
        $context = $PAGE->context;
    }
    if ($context->contextlevel == CONTEXT_MODULE) {
        $params['cmid'] = $context->instanceid;
    } else if ($context->contextlevel == CONTEXT_COURSE) {
        $params['courseid'] = $context->instanceid;
    }

    if (!is_null($preferredbehaviour)) {
        $params['behaviour'] = $preferredbehaviour;
    }

    if (!is_null($maxmark)) {
        $params['maxmark'] = $maxmark;
    }

    if (!is_null($displayoptions)) {
        $params['correctness']     = $displayoptions->correctness;
        $params['marks']           = $displayoptions->marks;
        $params['markdp']          = $displayoptions->markdp;
        $params['feedback']        = (bool) $displayoptions->feedback;
        $params['generalfeedback'] = (bool) $displayoptions->generalfeedback;
        $params['rightanswer']     = (bool) $displayoptions->rightanswer;
        $params['history']         = (bool) $displayoptions->history;
    }

    if ($variant) {
        $params['variant'] = $variant;
    }

    return new moodle_url($urlpath, $params);
}


/**
 * alternative to question_preview_action_url from preview.lib
 * with arbitrary path as last parameter - default: '/question/preview.php'
 * @param $questionid
 * @param $qubaid
 * @param $options
 * @param $context
 * @param $urlpath
 * @return moodle_url
 */
function question_general_action_url($qubaid,$options, $context, $urlpath = '/question/preview.php') {
    $params = array(
        'previewid' => $qubaid,
    );
    if ($context->contextlevel == CONTEXT_MODULE) {
        $params['cmid'] = $context->instanceid;
    } else if ($context->contextlevel == CONTEXT_COURSE) {
        $params['courseid'] = $context->instanceid;
    }
    $params = array_merge($params, $options->get_url_params());
    return new moodle_url($urlpath, $params);
}
<?php
/**
 * Created by Vojtěch Havel on 2014/12/13
 */
//moodle/question/engine/renderer.php

function question(question_attempt $qa, qbehaviour_renderer $behaviouroutput,
                  qtype_renderer $qtoutput, question_display_options $options, $number) {
    $output = '';
    $output .= html_writer::start_tag('div', array(
        'id' => 'q' . $qa->get_slot(),
        'class' => implode(' ', array(
            'que',
            $qa->get_question()->qtype->name(),
            $qa->get_behaviour_name(),
            $qa->get_state_class($options->correctness && $qa->has_marks()),
        ))
    ));
//    $output .= html_writer::tag('div',
//                    $this->info($qa, $behaviouroutput, $qtoutput, $options, $number),
//            array('class' => 'info'));
    $output .= html_writer::start_tag('div', array('class' => 'content'));

    $output .= html_writer::tag('div',
        $this->add_part_heading($qtoutput->formulation_heading(),
            $this->formulation($qa, $behaviouroutput, $qtoutput, $options)),
        array('class' => 'formulation'));

    $output .= html_writer::nonempty_tag('div',
        $this->add_part_heading(get_string('feedback', 'question'),
            $this->outcome($qa, $behaviouroutput, $qtoutput, $options)),
        array('class' => 'outcome'));


    $output .= html_writer::end_tag('div');
    $output .= html_writer::end_tag('div');
    return $output;
}
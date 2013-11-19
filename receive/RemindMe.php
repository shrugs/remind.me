<?php

/**
*
*/
class RemindMe
{

    private $regexes = array(
            array(
                    // remind me to (take the trash out) (tomorrow) at 5
                    'regex' => '/^([rR]emind me to )?([\s\S]+?) ((([sS]un|[mM]on|([tT](ues|hurs))|[fF]ri)(day|\.)?|[wW]ed(\.|nesday)?|[sS]at(\.|urday)?|[tT]((ue?)|(hu?r?))\.?)( (at )?(\w+)?)?|(tomorrow|today)( (at )?(\w+)?)?|(\d+|\w+) \w+ from now)$/',
                    'reminder_text_index' => 2,
                    'time_index' => 3
                ),

            array(
                    //remind me to (take out the trash) in (3 hours)
                    'regex' => '/^([rR]emind me to )?([\s\S]+?) in ([\s\S]+?)$/',
                    'reminder_text_index' => 2,
                    'time_index' => 3,
                    'add' => ' from now'
                ),

            array(
                    // (tomorrow) at (5),? remind me to (take out the trash)
                    'regex' => '/^((([sS]un|[mM]on|([tT](ues|hurs))|[fF]ri)(day|\.)?|[wW]ed(\.|nesday)?|[sS]at(\.|urday)?|[tT]((ue?)|(hu?r?))\.?)( (at )?(\w+)?)?|(tomorrow|today)( (at )?(\w+)?)?|(\d+|\w+) \w+ from now),? (remind me to )?([\s\S]+?)$/',
                    'reminder_text_index' => 19,
                    'time_index' => 1
                ),

            array(
                    // (tomorrow) at (5),? remind me to (take out the trash)
                    'regex' => '/^[iI]n ((\d+|\w+) \w+),? (remind me to )?([\s\S]+?)$/',
                    'reminder_text_index' => 4,
                    'time_index' => 1,
                    'add' => ' from now'
                )

            // array(
            //         // remind me to (take the trash out) (tomorrow)
            //         'regex' => '/^([rR]emind me to )?([\s\S]+?) (\w+?)$/',
            //         'reminder_text_index' => 2,
            //         'time_index' => 3
            //     ),

        );

    function parseStringForData($text)
    {

        // reminder_text_index
        // day_index
        // time_index

        $reminder_text = "";
        $time = NULL;


        foreach ($this->regexes as $i => $reg) {
            // try each to see if they match. If so, break and use that information
            if (preg_match($reg['regex'], $text, $matches)) {
                // yay!
                error_log(print_r($matches, true));
                $reminder_text = $matches[$reg['reminder_text_index']];
                $time = $matches[$reg['time_index']];

                if (isset($reg['add'])) {
                    $time .= $reg['add'];
                }
                // error_log("matched regex: " . $i);
                break;
            }
        }

        if ($time === NULL) {
            // nothing found
            return array('reminder_text' => NULL, 'time' => NULL);
        }

        return array('reminder_text' => $reminder_text, 'time' => $time);
    }
}

?>
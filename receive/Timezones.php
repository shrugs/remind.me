<?php


/**
*
*/
class TZ
{

    private $tzs = array(
        'Atlantic Daylight Time' => 'ADT',
        'Cental Daylight Time' => 'CDT',
        'Central European Daylight Time' => 'CEDT',
        'Central European Summer Time' => 'CEST',
        'Central European Time' => 'CET',
        'Central Standard Time' => 'CST',
        'Eastern Daylight Time' => 'EDT',
        'Eastern European Daylight Time' => 'EEDT',
        'Eastern European Summer Time' => 'EEST',
        'Eastern European Time' => 'EET',
        'Eastern Standard Time' => 'EST',
        'General Mountain Time' => 'GMT',
        'Hawaii-Aleutian Daylight Time' => 'HADT',
        'Hawaii-Aleutian Standard Time' => 'HAST',
        'Mountain Daylight Time' => 'MDT',
        'Moscow Daylight Time' => 'MSD',
        'Moscow Standard Time' => 'MSK',
        'Mountain Standard Time' => 'MST',
        'Paciic Daylight Time' => 'PDT',
        'Pacific Standard Time' => 'PST',
        'Coordinated Universal Time' => 'UTC',
        'Western European Daylight Time' => 'WEDT',
        'Western European Summer Time' => 'WEST',
        'Western European Time' => 'WET'
        );

    function getCodeForTimeZone($timezone)
    {
        return $this->tzs[$timezone];
    }
}

?>
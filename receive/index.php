<?php

require('RemindMe.php');
require('Timezones.php');
$text = $_POST['Body'];

$remindme = new RemindMe();
$r = $remindme->parseStringForData($text);
$reminder_text = $r['reminder_text'];
$time = $r['time'];

if ($reminder_text !== NULL && $time !== NULL) {
    // now find the time to send the reminder by accounting for timezone bullshit
    // first get the location of the zip the number was sent from
    // http://maps.googleapis.com/maps/api/geocode/json?components=postal_code:70460&sensor=false

    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, "http://maps.googleapis.com/maps/api/geocode/json?components=postal_code:".(string)(int)$_POST['FromZip']."&sensor=false");
    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // $output contains the output string
    $output = json_decode(curl_exec($ch), true);



    // then get the time zone
    // https://maps.googleapis.com/maps/api/timezone/json?location=39.6034810,-119.6822510&timestamp=1331161200&sensor=false
    $lat = $output['results'][0]['geometry']['location']['lat'];
    $lng = $output['results'][0]['geometry']['location']['lng'];
    curl_setopt($ch, CURLOPT_URL, "https://maps.googleapis.com/maps/api/timezone/json?location=".$lat.",".$lng."&timestamp=".strtotime("now")."&sensor=false");
    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // $output contains the output string
    $timezoneResults = json_decode(curl_exec($ch), true);



    // grab the time from the text in UTC
    // http://www.timeapi.org/utc/in+two+hours
    $tzShortCode = strtolower((new TZ())->getCodeForTimeZone($timezoneResults['timeZoneName']));
    // set url
    curl_setopt($ch, CURLOPT_URL, "http://www.timeapi.org/".$tzShortCode."/" . urlencode($time));
    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // $output contains the output string
    $timeOut = curl_exec($ch);
    curl_close($ch);

    // if error in $time
    if (strpos($timeOut, "error") !== false) {
        // oh noes...
        error_log("ERROR: BAD TIME: " . $time);
        exit();
    } else {
        $time = $timeOut;
    }


    $local = new DateTime($time, new DateTimeZone($timezoneResults['timeZoneId']));
    $utcTime = clone $local;
    $utcTime->setTimezone(new DateTimeZone('UTC'));


    // SWEET NOW LOG THAT in the db

    require('../../../lib/remind.me/db.php');
    try {
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name;", $db_user, $db_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare('INSERT INTO Reminders (MessageID, ReminderText, ToNum, TSDeliver, Sent, TSAdded) VALUES (:MessageID, :ReminderText, :ToNum, :TSDeliver, 0, NOW())');
        $stmt->execute(array(
            ':MessageID' => (string)$_POST['MessageSid'],
            ':ReminderText' => $reminder_text,
            ':ToNum' => (string)$_POST['From'],
            ':TSDeliver' => $utcTime->format('Y-m-d H:i:s')
            ));


    } catch(PDOException $e) {
        error_log('ERROR: ' . $e->getMessage());
        exit();
    }


    }

    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
<?php

    if ($reminder_text !== NULL && $time !== NULL) {
        echo "<Message>";

            echo " \n\nReminder: " . $reminder_text . "\n";
            echo "Time: " . $local->format("Y/m/d H:i:s") . "\n";

        echo "</Message>";
    } else {
        echo "<Message>";
            echo "We totally suck for messing this up... but could you try again with a different wording?";
        echo "</Message>";
    }

?>
</Response>
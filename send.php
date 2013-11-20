#! /usr/bin/php
<?php

// This file will send all unsent reminders when run via cron job
require('/var/www/condin/lib/remind.me/db.php');



try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // get now because apparently "NOW()" doesn't do shit
    $now = new DateTime(strtotime('now'));

    $stmt = $conn->prepare('SELECT MessageID, ReminderText, ToNum FROM Reminders WHERE Sent=0 AND TSDeliver<=:now');
    $stmt->execute(array(':now' => $now->format('Y-m-d H:i:s')));
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        exit();
    }

    // require twilio lib
    require('/var/www/condin/pub/remind.me/twilio-php-master/Services/Twilio.php');
    $client = new Services_Twilio($AccountSid, $AuthToken);
    foreach ($results as $i => $reminder) {
        // for each unsent reminder, send it using twilio


        $sms = $client->account->messages->sendMessage(
                $myNum,
                $reminder['ToNum'],
                $reminder['ReminderText']);

        // then set that message to sent in the db
        $stmt = $conn->prepare('UPDATE Reminders SET Sent=1 WHERE MessageID=:MessageID');
        $stmt->execute(array(':MessageID' => $reminder['MessageID']));

    }

    error_log("Successfully sent " . (string)count($results) . " reminders!");

} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}


?>
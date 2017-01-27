#BEGIN MESSAGE#
<?php
echo "EpochTime: ";
$dtz = new DateTimeZone('Europe/Rome');
$time_in_sofia = new DateTime('now', $dtz);
$offset=$dtz->getOffset( $time_in_sofia );
echo time()+$offset;
echo "\n";
echo "Offset: ";
echo $offset;
echo "\n";
?>
#END MESSAGE#
--TEST--
parallel cancellation (already cancelled)
--SKIPIF--
<?php
if (!extension_loaded('parallel')) {
	die("skip parallel not loaded");
}
?>
--FILE--
<?php
$parallel = new \parallel\Runtime();
$sync     = \parallel\Channel::make("sync");

$future = $parallel->run(function(){
    $sync = \parallel\Channel::open("sync");
    $sync->recv();

    while(1)
        usleep(10000);
});

$sync->send(true);

var_dump($future->cancel(), $future->cancelled());

try {
    $future->cancel();
} catch (\parallel\Future\Error\Cancelled $ex) {
    var_dump($ex->getMessage());
}
?>
--EXPECT--
bool(true)
bool(true)
string(26) "task was already cancelled"

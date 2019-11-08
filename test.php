<?php

require __DIR__.'/lib/Memcached.class.php';

$memcached = new \Trasigor\Memcached\Memcached();

$key = 'some_key';
$val = 'xyz';

echo "Getting value of the \"$key\" variable:\n";
echo $memcached->get($key)."\n";

echo "Setting value \"$val\" for the \"$key\" variable:\n";
$memcached->set($key, $val);

echo "Getting value of the \"$key\" variable:\n";
echo $memcached->get($key)."\n";

echo "Deleting \"$key\" variable:\n";
echo $memcached->delete($key)."\n";

echo "Getting value of the \"$key\" variable:\n";
echo $memcached->get($key)."\n";

# Memcached
Simple implementation of the basic functions of the Memcached protocol

## Installation

```
composer require trasigor/Memcached
```

## Usage

```php
use Trasigor\Memcached;

$mc = new Memcached\Memcached();
$mc->set('key', 'val');
$val = $mc->get('key');
$mc->delete('key');
```

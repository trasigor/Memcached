<?php

namespace Trasigor\Memcached\Tests;

use Trasigor\Memcached;
use PHPUnit\Framework\TestCase;

class MemcachedTest extends TestCase
{
    public function testCanBeCreated()
    {
        $this->assertInstanceOf(
            Memcached\Memcached::class,
            new Memcached\Memcached()
        );
    }

    public function testCannotBeCreated()
    {
        $this->expectException(\Exception::class);
        new Memcached\Memcached(25, 'localhost');
    }

    public function testCanSetIntMemcachedVariable()
    {
        $mc = new Memcached\Memcached();
        $this->assertTrue($mc->set('int', 1));
    }

    public function testCanSetFloatMemcachedVariable()
    {
        $mc = new Memcached\Memcached();
        $this->assertTrue($mc->set('float', 1.1));
    }

    public function testCanSetStringMemcachedVariable()
    {
        $mc = new Memcached\Memcached();
        $this->assertTrue($mc->set('string', 'val'));
    }

    public function testCanSetArrayMemcachedVariable()
    {
        $mc = new Memcached\Memcached();
        $this->assertTrue($mc->set('array', ['hello' => 'test']));
    }

    public function testCanSetObjectMemcachedVariable()
    {
        $object = new \stdClass();
        $object->prop = "value";

        $mc = new Memcached\Memcached();
        $this->assertTrue($mc->set('object', $object));
    }

    public function testCannotSetMemcachedVariableWithSpacesInKey()
    {
        $this->expectException(\Exception::class);
        $mc = new Memcached\Memcached();
        $mc->set('k e y', 'val');
    }

    public function testCannotSetMemcachedVariableWithNewLineSymbolInKey()
    {
        $this->expectException(\Exception::class);
        $mc = new Memcached\Memcached();
        $mc->set("key\n", 'val');
    }

    public function testCannotSetMemcachedVariableWithNullKey()
    {
        $this->expectException(\Exception::class);
        $mc = new Memcached\Memcached();
        $mc->set(null, 'val');
    }

    public function testCannotSetMemcachedVariableWithKeyLongerThen250Characters()
    {
        $this->expectException(\Exception::class);
        $mc = new Memcached\Memcached();
        $mc->set(str_repeat("a", 251), 'val');
    }

    public function testCanGetIntMemcachedVariable()
    {
        $mc = new Memcached\Memcached();
        $this->assertIsInt($mc->get('int'));
    }

    public function testCanGetFloatMemcachedVariable()
    {
        $mc = new Memcached\Memcached();
        $this->assertIsFloat($mc->get('float'));
    }

    public function testCanGetStringMemcachedVariable()
    {
        $mc = new Memcached\Memcached();
        $this->assertIsString($mc->get('string'));
    }

    public function testCanGetArrayMemcachedVariable()
    {
        $mc = new Memcached\Memcached();
        $this->assertIsArray($mc->get('array'));
    }

    public function testCanGetObjectMemcachedVariable()
    {
        $mc = new Memcached\Memcached();
        $this->assertIsObject($mc->get('object'));
    }

    public function testCanDeleteMemcachedVariable()
    {
        $mc = new Memcached\Memcached();
        $this->assertTrue($mc->delete('string'));
    }

    public function testCannotDeleteNotExistingMemcachedVariable()
    {
        $this->expectException(\Exception::class);
        $mc = new Memcached\Memcached();
        $mc->delete('string');
    }

}

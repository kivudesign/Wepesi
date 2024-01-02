<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

namespace Wepesi\Test;

use PHPUnit\Framework\TestCase;
use Wepesi\Core\Config;

class ConfigTest extends TestCase
{
    public function testNotGet()
    {
        $this->assertEquals(false, Config::get());
    }
    // the module shouldbe rewrite cause global variable break unit test
//    public  function testGetMiddleware()
//    {
////        $this->assertEquals(Config::get('middleware'),WEB_ROOT);
//    }
//
//    public  function testGetController()
//    {
////        $this->assertEquals(Config::get('controller'),WEB_ROOT);
//    }
}

<?php

/*
 * This file is part of core location package
 *
 * Copyright (c) 2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/corelocation
 *
 */

namespace Tuupola\CoreLocation;

use PHPUnit\Framework\TestCase;
use Whereami\Adapter\LocateMeAdapter;
use RuntimeException;

class RequestTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldGenerateHeader()
    {
        $hex = "00010005656e5f55530013636f6d2e6170706c652e6c6f636174696f6e64"
             . "000c382e342e312e31324833323100000001";

        $this->assertEquals(hex2bin($hex), (new Request)->header());
    }

    public function testShouldGeneratePayload()
    {
        $hex = "12130a1161613a61613a61613a61613a61613a616112130a1162623a6262"
             . "3a62623a62623a62623a62622064";

        $request = new Request(["aa:aa:aa:aa:aa:aa", "bb:bb:bb:bb:bb:bb"]);
        $this->assertEquals(hex2bin($hex), $request->payload());
    }

    public function testShouldGenerateLengthPrefixedBody()
    {
        $request = new Request(["aa:aa:aa:aa:aa:aa", "bb:bb:bb:bb:bb:bb"]);
        $header = $request->header();
        $payload = $request->payload();
        $body = $request->body();

        $length = substr($body, strlen($header));
        $length = substr($length, 0, strlen($payload) * -1);
        $length = unpack("N", $length)[1];

        $this->assertEquals($length, strlen($payload));
    }

    public function testShouldSetRouters()
    {
        $routers = ["aa:aa:aa:aa:aa:aa", "bb:bb:bb:bb:bb:bb"];
        $request = (new Request)->routers($routers);
        /* Closure kludge to test private properties. */
        $self = $this;
        $closure = function () use ($self, $routers) {
            $self->assertEquals($routers, $this->routers);
        };

        call_user_func($closure->bindTo($request, Request::class));
    }

    public function testShouldAddRouter()
    {
        $request = (new Request)->addRouter("cc:cc:cc:cc:cc:cc");
        $self = $this;
        $closure = function () use ($self) {
            $self->assertEquals(["cc:cc:cc:cc:cc:cc"], $this->routers);
        };

        call_user_func($closure->bindTo($request, Request::class));
    }

    public function testShouldThrowWithInvalidMac()
    {
        $this->expectException(RuntimeException::class);
        $request = (new Request)->addRouter("xx:cc:cc:cc:cc:cc");
    }

    public function testShouldSetLocale()
    {
        $request = (new Request)->locale("en_GB");
        $self = $this;
        $closure = function () use ($self) {
            $self->assertEquals("en_GB", $this->locale);
        };

        call_user_func($closure->bindTo($request, Request::class));
    }

    public function testShouldSetIdentifier()
    {
        $request = (new Request)->identifier("com.example.locationd");
        $self = $this;
        $closure = function () use ($self) {
            $self->assertEquals("com.example.locationd", $this->identifier);
        };

        call_user_func($closure->bindTo($request, Request::class));
    }

    public function testShouldSetVersion()
    {
        $request = (new Request)->version("10.3.114E304");
        $self = $this;
        $closure = function () use ($self) {
            $self->assertEquals("10.3.114E304", $this->version);
        };

        call_user_func($closure->bindTo($request, Request::class));
    }

    public function testShouldSetAmount()
    {
        $request = (new Request)->amount(50);
        $self = $this;
        $closure = function () use ($self) {
            $self->assertEquals(50, $this->amount);
        };

        call_user_func($closure->bindTo($request, Request::class));
    }

    public function testShouldAnnoyScriptKiddies()
    {
        $this->expectException(RuntimeException::class);
        $request = (new Request)->amount(1000);
    }

    public function testShouldHydrate()
    {
        $routers = ["aa:aa:aa:aa:aa:aa", "bb:bb:bb:bb:bb:bb"];
        $request = new Request($routers, ["locale" => "en_GB"]);
        $self = $this;
        $closure = function () use ($self) {
            $self->assertEquals("en_GB", $this->locale);
        };

        call_user_func($closure->bindTo($request, Request::class));

        $closure = function () use ($self, $routers) {
            $self->assertEquals($routers, $this->routers);
        };

        call_user_func($closure->bindTo($request, Request::class));
    }
}

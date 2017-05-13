<?php

/*
 * This file is part of the core location package
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

/* Protoc generates classes which are mixture of PSR-0 and PSR-4. */
use Apple\CoreLocation\Response as AppleResponse;
use RuntimeException;
use Countable;
use Iterator;
use ArrayAccess;

class Response implements Iterator, Countable, ArrayAccess
{
    private $routers = [];
    private $position = 0;

    public function __construct(array $routers = [])
    {
        $this->routers = $routers;
    }

    public function fromString($payload, $skip = 10)
    {
        $response = new AppleResponse;
        $response->mergeFromString(substr($payload, $skip));

        $routers = [];
        foreach ($response->getWifi() as $router) {
            $location = $router->getLocation();
            $this->routers[] = [
                "mac" => $router->getMac(),
                "latitude" => $location->getLatitude() * pow(10, -8),
                "longitude" => $location->getLongitude() * pow(10, -8),
                "accuracy" => $location->getHorizontalAccuracy(),
                "channel" => $router->getChannel(),
            ];
        };

        return $this;
    }

    /* Methods for Countable. */
    public function count()
    {
        return count($this->routers);
    }

    /* Methods for Iterator. */
    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->routers[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->routers[$this->position]);
    }

    /* Methods for ArrayAccess. */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->routers[] = $value;
        } else {
            $this->routers[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->routers[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->routers[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->routers[$offset]) ? $this->routers[$offset] : null;
    }
}

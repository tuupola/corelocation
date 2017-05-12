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
use Iterator;

class Response implements Iterator
{
    private $routers = [];
    private $position = 0;

    public function __construct($payload)
    {
        $this->routers = $this->parse($payload);
    }

    public function parse($payload)
    {
        $response = new AppleResponse;
        $response->mergeFromString(substr($payload, 10));

        $routers = [];
        foreach ($response->getWifi() as $router) {
            $location = $router->getLocation();
            $routers[] = [
                "mac" => $router->getMac(),
                "latitude" => $location->getLatitude() * pow(10, -8),
                "longitude" => $location->getLongitude() * pow(10, -8),
                "accuracy" => $location->getHorizontalAccuracy(),
                "channel" => $router->getChannel(),
            ];
        };

        return $routers;
    }

    public function routers()
    {
        return $this->routers;
    }

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
}

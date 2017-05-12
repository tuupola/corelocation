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
use Apple\CoreLocation\Request as AppleRequest;
use Apple\CoreLocation\Request_Router as AppleRequestRouter;
use RuntimeException;

class Request
{
    private $locale = "en_US";
    private $identifier = "com.apple.locationd";
    private $version = "8.4.1.12H321";
    private $routers = [];
    private $amount = 100;

    public function __construct(array $options = [])
    {
        $this->hydrate($options);
    }

    public function body()
    {
        $payload = $this->payload();
        return $this->header() . pack("N", strlen($payload)) . $payload;
    }

    public function header()
    {
        $header = pack("n", 0x0001) /* SOH? */
                . pack("n", strlen($this->locale))
                . $this->locale
                . pack("n", strlen($this->identifier))
                . $this->identifier
                . pack("n", strlen($this->version))
                . $this->version
                . pack("N", 0x00000001);

        return $header;
    }

    public function payload()
    {
        $request = new AppleRequest;
        array_walk($this->routers, function ($mac) use ($request) {
            $requestRouter = new AppleRequestRouter;
            $requestRouter->setMac($mac);
            $request->getRouter()[] = $requestRouter;
        });

        return $request->serializeToString();
    }

    public function routers(array $routers)
    {
        array_walk($routers, function ($mac) {
            $this->addRouter($mac);
        });
        return $this;
    }

    public function addRouter($mac)
    {
        if (!preg_match("/^([a-fA-F0-9]{2}[:|\-]?){6}$/", $mac)) {
            throw new RuntimeException("Invalid mac {$mac}");
        }
        $this->routers[] = $mac;
        return $this;
    }

    public function locale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    public function identifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function version($version)
    {
        $this->version = $version;
        return $this;
    }

    public function amount($amount)
    {
        if ($amount > 500) {
            throw new RuntimeException("Abuse is bad mmmkay?");
        }
        return $this;
    }

    public function hydrate($data = [])
    {
        foreach ($data as $key => $value) {
            /* https://github.com/facebook/hhvm/issues/6368 */
            $key = str_replace(".", " ", $key);
            $method = ucwords($key);
            $method = str_replace(" ", "", $method);
            $method = lcfirst($method);
            if (method_exists($this, $method)) {
                /* Try to use setter */
                call_user_func([$this, $method], $value);
            } else {
                /* Or fallback to setting property directly */
                $this->$key = $value;
            }
        }
    }
}

<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: corelocation.proto

namespace Apple\CoreLocation;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Protobuf type <code>Apple.CoreLocation.Request.Router</code>
 */
class Request_Router extends \Google\Protobuf\Internal\Message
{
    /**
     * <code>string mac = 1;</code>
     */
    private $mac = '';

    public function __construct() {
        \GPBMetadata\Corelocation::initOnce();
        parent::__construct();
    }

    /**
     * <code>string mac = 1;</code>
     */
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * <code>string mac = 1;</code>
     */
    public function setMac($var)
    {
        GPBUtil::checkString($var, True);
        $this->mac = $var;
    }

}

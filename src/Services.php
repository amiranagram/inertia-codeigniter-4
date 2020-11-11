<?php

namespace Inertia;

use CodeIgniter\Config\Services as BaseServices;

class Services extends BaseServices
{
    public static function inertia($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('inertia');
        }

        return new Factory;
    }
}

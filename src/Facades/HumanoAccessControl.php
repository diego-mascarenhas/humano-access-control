<?php

namespace Idoneo\HumanoAccessControl\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Idoneo\HumanoAccessControl\HumanoAccessControl
 */
class HumanoAccessControl extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Idoneo\HumanoAccessControl\HumanoAccessControl::class;
    }
}

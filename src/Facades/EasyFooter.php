<?php

namespace Agelgil\FilamentEasyFooter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Agelgil\FilamentEasyFooter\EasyFooter
 */
class EasyFooter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Agelgil\FilamentEasyFooter\EasyFooter::class;
    }
}

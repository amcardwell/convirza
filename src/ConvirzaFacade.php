<?php

namespace Skidaatl\Convirza;

use Illuminate\Support\Facades\Facade;

class ConvirzaFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'convirza';
    }
}

<?php

namespace Caffeinated\Modules\Exceptions;

class ModuleNotFoundException extends \Exception
{
    /**
     * ModuleNotFoundException constructor.
     * @param $slug
     */
    public function __construct($slug)
    {
        parent::__construct('Module with slug name [' . $slug . '] not found');
    }
}
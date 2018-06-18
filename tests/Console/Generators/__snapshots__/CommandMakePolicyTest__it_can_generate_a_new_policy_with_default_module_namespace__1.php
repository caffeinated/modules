<?php return '<?php

namespace App\\Modules\\Policy\\Policies;

use Illuminate\\Auth\\Access\\HandlesAuthorization;

class DefaultPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
}
';

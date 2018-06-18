<?php return '<?php

namespace App\\CustomPolicyNamespace\\Policy\\Policies;

use Illuminate\\Auth\\Access\\HandlesAuthorization;

class CustomPolicy
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

<?php return '<?php

namespace App\\Modules\\Resource\\Http\\Resources;

use Illuminate\\Http\\Resources\\Json\\ResourceCollection;

class DefaultResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \\Illuminate\\Http\\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
';

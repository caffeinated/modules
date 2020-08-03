<?php return '<?php

namespace App\\Modules\\Resource\\Http\\Resources;

use Illuminate\\Http\\Resources\\Json\\JsonResource;

class DefaultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
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

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Bellitalia as BellitaliaResource;

class Supplement extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
   public function toArray($request)
   {
     return [
       'id' => $this->id,
       'name' => $this->name,
       'bellitalia' => new BellitaliaResource($this->bellitalia),


     ];
   }

   public function withResponse($request, $response)
   {
     $response->header('X-Value', 'True');
   }
}

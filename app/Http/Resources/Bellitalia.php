<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Bellitalia extends JsonResource
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
      'number' => $this->number,
      'publication' => $this->publication,
      'image' => $this->image,
    ];
  }

  public function withResponse($request, $response)
  {
    $response->header('X-Value', 'True');
  }
}

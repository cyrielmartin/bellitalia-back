<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Supplement as SupplementResource;

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
      'supplements' => SupplementResource::collection($this->supplements),
    ];
  }

  public function withResponse($request, $response)
  {
    $response->header('X-Value', 'True');
  }
}

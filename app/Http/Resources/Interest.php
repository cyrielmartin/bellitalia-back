<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\City as CityResource;
use App\Http\Resources\Bellitalia as BellitaliaResource;
use App\Http\Resources\Tag as TagResource;
use App\Http\Resources\Image as ImageResource;


class Interest extends JsonResource
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
      'description' => $this->description,
      'link' => $this->link,
      'latitude' => $this->latitude,
      'longitude' => $this->longitude,
      'city' => new CityResource($this->city),
      'bellitalia' => new BellitaliaResource($this->bellitalia),
      'tags' => TagResource::collection($this->tags),
      'images' => ImageResource::collection($this->images),
    ];
  }

  public function withResponse($request, $response)
  {
    $response->header('X-Value', 'True');
  }
}

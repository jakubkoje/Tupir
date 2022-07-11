<?php namespace AppTupir\User\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use AppTupir\Catchphrase\Models\Catchphrase;

class SimpleUserResource extends Resource
{
    public function toArray($request)

    {
        $response = [
            'type'               => 'user',
            'id'                 => $this->id,
            'name'               => $this->name,
            'username'           => $this->username,
            'bio'                => $this->bio,
            'email'              => $this->email,
            'catchphrases_count' => Catchphrase::isPublished()->where('user_id', $this->id)->count(),
            'avatar'             => $this->avatar,
        ];

        return $response;
    }
}




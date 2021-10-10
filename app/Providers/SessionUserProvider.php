<?php

namespace App\Providers;


use App\Models\Session;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

class SessionUserProvider implements UserProvider
{

    private $store;

    /**
     * SessionUserProvider constructor.
     * @param Repository $store
     */
    public function __construct(Repository $store)
    {
        $this->store = $store;
    }


    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return new Session(
            $this->getUniqueTokenForSession($identifier)
        );
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        return;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        return null;
    }

    private function unpack($data)
    {
        return json_decode($data);
    }

    private function getUniqueTokenForSession($id)
    {
        return $this->retrieveCacheDataForSession($id)
            ->get('uuid');
    }

    private function retrieveCacheDataForSession($id)
    {
        $fluent = new Fluent(
            $this->unpack(
                $this->store->has($id) ? $this->store->get($id) : "[]"
            )
        );

        if(!$fluent->__isset('uuid')) {
            $fluent->__set('uuid', Cookie::get('Chat_Cookie'));
        }
        $this->store->put($id, $fluent->toJson(), 1);
        return $fluent;

    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return null;
    }
}
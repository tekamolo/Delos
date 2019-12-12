<?php

namespace Delos\Repository;

use Delos\Model\User;

class UserRepository
{
    /**
     * @return User[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll(){
        return User::all();
    }

    public function createUser(User $user){
        User::create(['username'=>$user->username,'email'=>$user->email,'password'=>$user->password]);
    }
}
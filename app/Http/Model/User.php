<?php

namespace App\Http\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nom', 'sexe', 'prenom', 'tel', 'mail', 'adresse', 'login', 'password', 'Role', 'other', 'image', 'user_action', 'action_save', 'auth'
    ]; 

    protected $primaryKey = "idUser";
    //protected $table = "";

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}

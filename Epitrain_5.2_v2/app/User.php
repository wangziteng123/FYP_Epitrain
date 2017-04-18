<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
/**
 * User Class used for user
 */
class User extends Authenticatable
{
    use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	/**
    *checks if a user is an Admin
    *
    * @return (TINYINT)(isAdmin)
    */
    public function isAdmin() {
        return $this->isAdmin;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password-confirm' => 'required',
          ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Name field is blank',
            'email.required'  => 'Email address is missing',
            'email.email'  => 'Email is not in correct format',
            'password.required'  => 'Password field is blank',
        ];
    }
}

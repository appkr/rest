<?php

namespace App;

use Bican\Roles\Traits\HasRoleAndPermission;
use Bican\Roles\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;
use App\Contracts\SocialAccountContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract, SocialAccountContract, HasRoleAndPermissionContract
{
    use Authenticatable;
    use CanResetPassword;
    use HasRoleAndPermission;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'activation_code'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_login',
        'activated_at'
    ];

    # Relationships

    public function profile()
    {
        return $this->hasOne(\App\Profile::class);
    }

    public function socials()
    {
        return $this->hasMany(\App\Social::class);
    }

    public function todos()
    {
        return $this->hasMany(\App\Todo::class);
    }

    public function setting()
    {
        return $this->hasOne(\App\UserSetting::class);
    }

    # Interface implementation

    /**
     * Get Oauth provider name
     *
     * @return array
     */
    public function getSocialProviders()
    {
        return $this->socials->lists('provider')->toArray();
    }

    /**
     * Get user id obtained from Oauth provider
     *
     * @return array
     */
    public function getSocialIds()
    {
        return $this->socials->lists('provider_id')->toArray();
    }

    /**
     * Get nickname obtained from Oauth provider
     *
     * @return array
     */
    public function getNicknames()
    {
        return $this->socials->lists('nickname')->toArray();
    }

    /**
     * Check the current user is registered with an Oauth provider or not.
     * Return true if he/she is, otherwise false.
     *
     * @return bool
     */
    public function isSocialUser()
    {
        return ($this->getSocialProviders()
            && $this->getAuthPassword() == null)
            ? true : false;
    }
}
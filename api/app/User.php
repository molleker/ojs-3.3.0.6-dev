<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\User
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $unreadNotifications
 * @mixin \Eloquent
 * @property integer $user_id
 * @property string $username
 * @property string $password
 * @property string $salutation
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $suffix
 * @property string $gender
 * @property string $initials
 * @property string $email
 * @property string $url
 * @property string $phone
 * @property string $fax
 * @property string $mailing_address
 * @property string $billing_address
 * @property string $country
 * @property string $locales
 * @property string $date_last_email
 * @property string $date_registered
 * @property string $date_validated
 * @property string $date_last_login
 * @property boolean $must_change_password
 * @property integer $auth_id
 * @property string $auth_str
 * @property boolean $disabled
 * @property string $disabled_reason
 * @property boolean $inline_help
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereSalutation($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereMiddleName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereSuffix($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereGender($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereInitials($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereFax($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereMailingAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereBillingAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereLocales($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereDateLastEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereDateRegistered($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereDateValidated($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereDateLastLogin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereMustChangePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereAuthId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereAuthStr($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereDisabled($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereDisabledReason($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereInlineHelp($value)
 * @property string $api_token
 * @method static \Illuminate\Database\Query\Builder|\App\User whereApiToken($value)
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $readNotifications
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';

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

    public function journalsByRole($role_id)
    {
        return Role::with('journal')->where([
            'role_id' => $role_id,
            'user_id' => $this->user_id
        ])->get()->pluck('journal');
    }
}

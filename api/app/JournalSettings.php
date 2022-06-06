<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * App\JournalSettings
 *
 * @property integer $journal_id
 * @property string $locale
 * @property string $setting_name
 * @property string $setting_value
 * @property string $setting_type
 * @method static \Illuminate\Database\Query\Builder|\App\JournalSettings whereJournalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\JournalSettings whereLocale($value)
 * @method static \Illuminate\Database\Query\Builder|\App\JournalSettings whereSettingName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\JournalSettings whereSettingValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\JournalSettings whereSettingType($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $unreadNotifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $readNotifications
 */
class JournalSettings extends Model
{
    use Notifiable;
    public $timestamps = false;

    public function routeNotificationForMail()
    {
        return $this->setting_value;
    }
}

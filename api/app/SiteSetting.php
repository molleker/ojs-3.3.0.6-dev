<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SiteSetting
 *
 * @property string $setting_name
 * @property string $locale
 * @property string $setting_value
 * @property string $setting_type
 * @method static \Illuminate\Database\Query\Builder|\App\SiteSetting whereSettingName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SiteSetting whereLocale($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SiteSetting whereSettingValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SiteSetting whereSettingType($value)
 * @mixin \Eloquent
 */
class SiteSetting extends \Eloquent
{
    public static function byAlias($alias)
    {
        $setting = self::where('setting_name', $alias)->where('locale', 'ru_RU')->first();
        return $setting ? $setting->setting_value : null;
    }
}

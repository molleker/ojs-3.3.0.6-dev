<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SectionSetting
 *
 * @property int $section_id
 * @property string $locale
 * @property string $setting_name
 * @property string $setting_value
 * @property string $setting_type
 * @method static \Illuminate\Database\Query\Builder|\App\SectionSetting whereSectionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SectionSetting whereLocale($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SectionSetting whereSettingName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SectionSetting whereSettingValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SectionSetting whereSettingType($value)
 * @mixin \Eloquent
 */
class SectionSetting extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'section_id';
}

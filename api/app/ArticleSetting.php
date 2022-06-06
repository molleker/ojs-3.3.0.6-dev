<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ArticleSetting
 *
 * @property integer $article_id
 * @property string $locale
 * @property string $setting_name
 * @property string $setting_value
 * @property string $setting_type
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleSetting whereArticleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleSetting whereLocale($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleSetting whereSettingName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleSetting whereSettingValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleSetting whereSettingType($value)
 * @mixin \Eloquent
 */
class ArticleSetting extends Model
{
    public $timestamps = false;
    protected $primaryKey = null;

}

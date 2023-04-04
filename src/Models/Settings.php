<?php

namespace sergeynilov\QuizzesInit\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Settings extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasTranslations;

    protected $table      = 'settings';
    protected $primaryKey = 'id';
    public $timestamps    = true;
    protected $fillable   = [
        'name',
        'value',
        'updated_at',
    ];
    public $translatable = ['value'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public static function scopeGetByName($query, $name = '')
    {
        if(!empty($name)) {
            return $query->where(with(new Settings)->getTable() . '.name', '=', $name);
        }
        return $query;
    }

    public static function getSettingsList($name = '')
    {
        $settingsValuesList = Settings::orderBy('id', 'asc')
            ->getByName($name)
            ->select('id', 'name', 'value')
            ->get();

        return $settingsValuesList;
    }

    public static function getValue($name, int $checkValueType = null, $default_value = null)
    {
        $settingsValue = Settings::getByName($name)->first();

        if(empty($settingsValue)) return $default_value;

        $value = trim($settingsValue->value);
        if (!isset($value)) {
            return $default_value;
        }
        return $value;
    }

}

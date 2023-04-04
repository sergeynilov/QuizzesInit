<?php

namespace sergeynilov\QuizzesInit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserQuizRequestCommunicationChannel extends Model
{

    protected $table      = 'user_quiz_request_communication_channels';
    protected $primaryKey = 'id';
    public $timestamps    = true;
    protected $fillable   = ['user_quiz_request_id', 'type', 'channel'];

    public function userQuizRequest(): BelongsTo
    {
        return $this->belongsTo(UserQuizRequest::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function scopeGetByType($query, $type = null)
    {
        if (! isset($type)) {
            return $query;
        }

        return $query->where($this->table.'.type', $type);
    }

    public function scopeGetById($query, $id)
    {
        return $query->where($this->table.'.id', $id);
    }

    public const USER_QUIZ_REQUEST_COMMUNICATION_CHANNEL_SKYPE_TYPE = 'S';
    public const USER_QUIZ_REQUEST_COMMUNICATION_CHANNEL_PHONE_TYPE = 'P';
    public const USER_QUIZ_REQUEST_COMMUNICATION_CHANNEL_TELEGRAM_TYPE = 'T';

    private static $UserQuizRequestCommunicationChannelTypeSelectionItems
        = [
            self::USER_QUIZ_REQUEST_COMMUNICATION_CHANNEL_SKYPE_TYPE   => 'Skype name',
            self::USER_QUIZ_REQUEST_COMMUNICATION_CHANNEL_PHONE_TYPE   => 'Phone number',
            self::USER_QUIZ_REQUEST_COMMUNICATION_CHANNEL_TELEGRAM_TYPE   => 'Telegram channel',
        ];

    public static function getCommunicationChannelTypeSelectionItems($keyReturn = true): array
    {
        $resArray = [];
        foreach (self::$UserQuizRequestCommunicationChannelTypeSelectionItems as $key => $value) {
            if ($keyReturn) {
                $resArray[] = ['key' => $key, 'label' => $value];
            } else {
                $resArray[$key] = $value;
            }
        }

        return $resArray;
    }

    public static function getCommunicationChannelTypeLabel(string $type): string
    {
        if (! empty(self::$UserQuizRequestCommunicationChannelTypeSelectionItems[$type])) {
            return self::$UserQuizRequestCommunicationChannelTypeSelectionItems[$type];
        }

        return self::$UserQuizRequestCommunicationChannelTypeSelectionItems[0];
    }
}

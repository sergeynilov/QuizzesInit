<?php

namespace sergeynilov\QuizzesInit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class UserMeeting extends Model
{
    protected $table      = 'user_meetings';
    protected $primaryKey = 'id';
    public $timestamps    = true;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'user_name',
        'user_email',
        'user_quiz_request_id',
        'appointed_at',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointed_at' => 'datetime',
    ];

    protected $touches = ['userQuizRequest'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public const USER_MEETING_STATUS_WAITING_FOR_REVIEW   = 'W';
    public const USER_MEETING_STATUS_ACCEPTED_FOR_MEETING   = 'A';
    public const USER_MEETING_STATUS_MARKED_FOR_FUTURE_CONTACTS   = 'M';
    public const USER_MEETING_STATUS_CANCELLED = 'C';
    public const USER_MEETING_STATUS_DECLINED = 'D';

    private static $userMeetingSelectionItems
        = [
            self::USER_MEETING_STATUS_WAITING_FOR_REVIEW   => 'Waiting for review',
            self::USER_MEETING_STATUS_ACCEPTED_FOR_MEETING   => 'Accepted for meeting',
            self::USER_MEETING_STATUS_MARKED_FOR_FUTURE_CONTACTS   => 'Marked for future contacts',
            self::USER_MEETING_STATUS_CANCELLED => 'Cancelled',
            self::USER_MEETING_STATUS_DECLINED => 'Declined',
        ];
    public static function getUserMeetingSelectionItems(bool $keyReturn = true): array
    {
        $resArray = [];
        foreach (self::$userMeetingSelectionItems as $key => $value) {
            if ($keyReturn) {
                $resArray[] = ['key' => $key, 'label' => $value];
            } else {
                $resArray[$key] = $value;
            }
        }

        return $resArray;
    }


    public function scopeGetByUserQuizRequestId($query, int $userQuizRequest = null)
    {
        if (! empty($userQuizRequest)) {
            $query->where($this->table.'.user_quiz_request_id', $userQuizRequest);
        }

        return $query;
    }

    public function userQuizRequest(): BelongsTo
    {
        return $this->belongsTo(UserQuizRequest::class);
    }



    public static function getUserMeetingLabel(string $userMeeting): string
    {
        if (! empty(self::$userMeetingSelectionItems[$userMeeting])) {
            return self::$userMeetingSelectionItems[$userMeeting];
        }

        return self::$userMeetingSelectionItems[self::USER_MEETING_STATUS_WAITING_FOR_REVIEW];
    }

    public function scopeGetById($query, $id)
    {
        return $query->where($this->table.'.id', $id);
    }


    public function scopeGetByStatus($query, $status = null)
    {
        if (! isset($status)) {
            return $query;
        }

        return $query->where($this->table.'.status', $status);
    }

    public static function getValidationRulesArray($userMeetingId = null, array $skipFieldsArray = []): array
    {
        $validationRulesArray = [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'user_name' => [
                'required',
                'string',
                'max:100',
            ],
            'user_email' => [
                'required',
                'email',
                'string',
                'max:100',
            ],
            'appointed_at'   => 'required|datetime',
            'status'      => 'required', // |in:' . getValueLabelKeys(UserMeeting::getUserMeetingSelectionItems(false)),
        ];
        foreach ($skipFieldsArray as $next_field) {
            if (! empty($validationRulesArray[$next_field])) {
                $validationRulesArray = Arr::except($validationRulesArray, $next_field);
            }
        }

        return $validationRulesArray;
    }

}

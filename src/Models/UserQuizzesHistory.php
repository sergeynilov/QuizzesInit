<?php

namespace sergeynilov\QuizzesInit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class UserQuizzesHistory extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table      = 'user_quizzes_history';
    protected $primaryKey = 'id';
    public $timestamps    = true;
    protected $fillable   = ['user_quiz_request_id', 'quiz_category_id', 'quiz_category_name', 'user_name',
                             'selected_locale', 'user_email', 'time_spent', 'summary_points', 'max_summary_points', 'is_reviewed', 'action'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    protected $casts = [];

    public const IS_REVIEWED_YES = 1;
    public const IS_REVIEWED_NO  = 0;

    private static $IsReviewedSelectionItems
        = [
            self::IS_REVIEWED_NO  => 'Not reviewed',
            self::IS_REVIEWED_YES => 'Is reviewed',
        ];

    public static function getIsReviewedSelectionItems(bool $keyReturn = true): array
    {
        $resArray = [];
        foreach (self::$IsReviewedSelectionItems as $key => $value) {
            if ($keyReturn) {
                $resArray[] = ['key' => $key, 'label' => $value];
            } else {
                $resArray[$key] = $value;
            }
        }

        return $resArray;
    }

    public static function getIsReviewedLabel(string $IsReviewed): string
    {
        if (! empty(self::$IsReviewedSelectionItems[$IsReviewed])) {
            return self::$IsReviewedSelectionItems[$IsReviewed];
        }

        return self::$IsReviewedSelectionItems[self::IS_REVIEWED_NO];
    }

    public function scopeGetByIsReviewed($query, string $IsReviewed = null)
    {
        if (! empty($IsReviewed)) {
            $query->where($this->table.'.is_reviewed', $IsReviewed);
        }

        return $query;
    }

    public function scopeGetByUserId($query, int $userId = null)
    {
        if (! empty($userId)) {
            $query->where($this->table.'.user_id', $userId);
        }

        return $query;
    }

    public function scopeGetByUserQuizRequestId($query, int $userQuizRequestId = null)
    {
        if (! empty($userQuizRequestId)) {
            $query->where($this->table.'.user_quiz_request_id', $userQuizRequestId);
        }

        return $query;
    }

    public function scopeGetByQuizCategoryId($query, int $quizCategoryId = null)
    {
        if (! empty($quizCategoryId)) {
            $query->where($this->table.'.quiz_category_id', $quizCategoryId);
        }

        return $query;
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function quizCategory(): BelongsTo
    {
        return $this->belongsTo(QuizCategory::class);
    }

    public function scopeGetById($query, $id)
    {
        return $query->where($this->table.'.id', $id);
    }

    public function userQuizzesHistoryDetails(): HasMany
    {
        return $this->hasMany(UserQuizzesHistoryDetail::class);
    }

    public function userQuizzesHistoryDetailsWithCorrectAnswers(): HasMany
    {
        return $this->hasMany(UserQuizzesHistoryDetail::class)->where('is_correct', 1);
    }


}

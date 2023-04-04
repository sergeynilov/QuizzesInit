<?php

namespace sergeynilov\QuizzesInit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class UserQuizzesHistoryDetail extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table      = 'user_quizzes_history_details';
    protected $primaryKey = 'id';
    public $timestamps    = false;
    protected $fillable   = ['user_quizzes_history_id', 'quiz_answer_id', 'text', 'is_correct', 'quiz_points'];

    protected static $UserQuizzesHistoryDetailIsCorrectLabelValueArray = [1 => 'Is Correct', 0 => 'Is Not Correct'];
    protected $casts                                                 = [];

    public function userQuizzesHistory(): BelongsTo
    {
        return $this->belongsTo(UserQuizzesHistory::class);
    }

    public function quizAnswer(): BelongsTo
    {
        return $this->belongsTo(QuizAnswer::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function scopeGetByIsCorrect($query, $isCorrect = null)
    {
        if (! isset($isCorrect)) {
            return $query;
        }

        return $query->where($this->table.'.is_correct', $isCorrect);
    }

    public function scopeGetById($query, $id)
    {
        return $query->where($this->table.'.id', $id);
    }

    public function scopeGetByQuizId($query, $quizId = null)
    {
        if (! empty($quizId)) {
            if (is_array($quizId)) {
                $query->whereIn($this->table.'.quiz_id', $quizId);
            } else {
                $query->where($this->table.'.quiz_id', $quizId);
            }
        }

        return $query;
    }

    public static function getIsCorrectValueArray($keyReturn = true): array
    {
        $resArray = [];
        foreach (self::$UserQuizzesHistoryDetailIsCorrectLabelValueArray as $key => $value) {
            if ($keyReturn) {
                $resArray[] = ['key' => $key, 'label' => $value];
            } else {
                $resArray[$key] = $value;
            }
        }

        return $resArray;
    }

    public static function getIsCorrectLabel(string $isCorrect): string
    {
        if (! empty(self::$UserQuizzesHistoryDetailIsCorrectLabelValueArray[$isCorrect])) {
            return self::$UserQuizzesHistoryDetailIsCorrectLabelValueArray[$isCorrect];
        }

        return self::$UserQuizzesHistoryDetailIsCorrectLabelValueArray[0];
    }

}

<?php

namespace sergeynilov\QuizzesInit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class QuizAnswer extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasTranslations;

    protected $table = 'quiz_answers';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['name', 'ordering', 'quiz_id', 'is_correct'];
    public $translatable = ['text'];
    private static $QuizAnswerIsCorrectLabelValueArray = [1 => 'Is Correct', 0 => 'Is Not Correct'];
    protected $casts = [];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function scopeGetByIsCorrect($query, $isCorrect = null)
    {
        if ( ! isset($isCorrect)) {
            return $query;
        }

        return $query->where($this->table . '.is_correct', $isCorrect);
    }

    public function scopeGetById($query, $id)
    {
        return $query->where($this->table . '.id', $id);
    }

    public function scopeGetByQuizId($query, $quizId = null)
    {
        if ( ! empty($quizId)) {
            if (is_array($quizId)) {
                $query->whereIn($this->table . '.quiz_id', $quizId);
            } else {
                $query->where($this->table . '.quiz_id', $quizId);
            }
        }

        return $query;
    }

    public static function getIsCorrectValueArray($keyReturn = true): array
    {
        $resArray = [];
        foreach (self::$QuizAnswerIsCorrectLabelValueArray as $key => $value) {
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
        if ( ! empty(self::$QuizAnswerIsCorrectLabelValueArray[$isCorrect])) {
            return self::$QuizAnswerIsCorrectLabelValueArray[$isCorrect];
        }

        return self::$QuizAnswerIsCorrectLabelValueArray[0];
    }

    public static function getValidationRulesArray($quizId, $QuizAnswerId = null, array $skipFieldsArray = []): array
    {
        $additionalItemValueValidationRule = 'check_quiz_answer_unique_by_name:' . $quizId . ',' . ( ! empty($QuizAnswerId) ? $QuizAnswerId : '');
        $validationRulesArray              = [
            'quiz_id'    => 'required|exists:' . ((new Quiz)->getTable()) . ',id',
            'name'       => 'required|max:255|' . $additionalItemValueValidationRule,
            'is_correct' => 'nullable|boolean',
            'ordering'   => 'nullable|integer',
        ];

        foreach ($skipFieldsArray as $next_field) {
            if ( ! empty($validationRulesArray[$next_field])) {
                $validationRulesArray = Arr::except($validationRulesArray, $next_field);
            }
        }
    }

    public static function getValidationMessagesArray(): array
    {
        return [
            'name.required'                    => 'Name is required',
            'check_quiz_answer_unique_by_name' => 'Quiz item must have unique name inside of a quiz',
            'is_correct.required'              => 'Is correct is required',
            'ordering.required'                => 'Ordering is required',
            'ordering.invalid'                 => 'Ordering is invalid. Must be valid integer',
        ];
    }
}

<?php

///_wwwroot/lar/quizzes/packages/sergeynilov/QuizzesInit/src/Models/UserQuizRequestCommunicationChannel.php
namespace sergeynilov\QuizzesInit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quiz extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $table      = 'quizzes';
    protected $primaryKey = 'id';
    public $translatable  = ['question'];
    public $timestamps    = true;
    protected $casts      = [];
    protected $fillable
        = [
            'question',
            'points',
            'quiz_category_id',
            'active',
        ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    protected $guarded = ['created_at'];

    public const QUIZ_STATUS_ACTIVE   = 1;
    public const QUIZ_STATUS_INACTIVE = 0;

    private static $quizStatusSelectionItems
        = [
            self::QUIZ_STATUS_ACTIVE   => 'Is active',
            self::QUIZ_STATUS_INACTIVE => 'Is not active',
        ];

    public static function getQuizStatusSelectionItems(bool $keyReturn = true): array
    {
        $resArray = [];
        foreach (self::$quizStatusSelectionItems as $key => $value) {
            if ($keyReturn) {
                $resArray[] = ['key' => $key, 'label' => $value];
            } else {
                $resArray[$key] = $value;
            }
        }

        return $resArray;
    }

    public static function getQuizStatusLabel(string $quizStatus): string
    {
        if (! empty(self::$quizStatusSelectionItems[$quizStatus])) {
            return self::$quizStatusSelectionItems[$quizStatus];
        }

        return self::$quizStatusSelectionItems[self::QUIZ_STATUS_INACTIVE];
    }

    public function scopeGetByIncludeInactive($query, $includeInactive = null)
    {
        if ($includeInactive) {
            return $query;
        }

        return $query->where($this->table.'.active', true);
    }

    public function scopeGetByActive($query, $active = null)
    {
        if (! isset($active)) {
            return $query;
        }

        return $query->where($this->table.'.active', (bool) $active);
    }

    public function scopeGetLocale($query, $locale = null)
    {
        if (! isset($locale)) {
            return $query;
        }

        return $query->where($this->table.'.question-'.$locale, (bool) $active);
    }

    public function scopeGetByQuestion($query, $question = null, $partial = false)
    {
        if (empty($question)) {
            return $query;
        }

        return $query->where(
            $this->table.'.question',
            (! $partial ? '=' : 'like'),
            ($partial ? '%' : '').$question.($partial ? '%' : '')
        );
    }

    public function scopeGetById($query, $id)
    {
        return $query->where($this->table.'.id', $id);
    }

    public function scopeGetByQuizCategoryId($query, $quizCategoryId)
    {
        if (!empty($quizCategoryId)) {
            if ( is_array($quizCategoryId) ) {
                $query->whereIn($this->table . '.quiz_category_id', $quizCategoryId);
            } else {
                return $query->where($this->table . '.quiz_category_id', $quizCategoryId);
            }
        }
    }

    public function quizCategory(): BelongsTo
    {
        return $this->belongsTo(QuizCategory::class);
    }

    public function quizAnswers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    public static function getValidationRulesArray($quizId = null, array $skipFieldsArray = []): array
    {
        $table                = (new Quiz)->getTable();
        $validationRulesArray = [
            'question' => [
                'required',
                'string',
                'max:255',
                Rule::unique($table->ignore($quizId)),
            ],
            'quiz_category_id'   => 'required|exists:'.((new QuizCategory)->getTable()).',id',
            'points'   => 'nullable|number',
            'active'   => 'nullable|boolean',
        ];
        foreach ($skipFieldsArray as $next_field) {
            if (! empty($validationRulesArray[$next_field])) {
                $validationRulesArray = Arr::except($validationRulesArray, $next_field);
            }
        }

        return $validationRulesArray;
    }

    public static function getValidationMessagesArray(): array
    {
        return [
            'question.required' => 'Question is required',
            'points.invalid'    => 'Points must be valid integer value',
        ];
    }
}

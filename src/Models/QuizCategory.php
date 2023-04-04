<?php

namespace sergeynilov\QuizzesInit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;
use Spatie\Translatable\HasTranslations;

class QuizCategory extends Model
{
    use HasTranslations;

    protected $table = 'quiz_categories';
    public $translatable = ['name'];
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['name', 'active'];
    private static $quizCategoryActiveLabelValueArray = ['1' => 'Active', '0' => 'Inactive'];

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }


    public static function getActiveValueArray($keyReturn = true): array
    {
        $resArray = [];
        foreach (self::$quizCategoryActiveLabelValueArray as $key => $value) {
            if ($keyReturn) {
                $resArray[] = ['key' => $key, 'label' => $value];
            } else {
                $resArray[$key] = $value;
            }
        }

        return $resArray;
    }

    public static function getActiveLabel(string $active): string
    {
        if ( ! empty(self::$quizCategoryActiveLabelValueArray[$active])) {
            return self::$quizCategoryActiveLabelValueArray[$active];
        }

        return self::$quizCategoryActiveLabelValueArray[0];
    }

    public function scopeGetByName($query, $name = null, $partial = false)
    {
        if (empty($name)) {
            return $query;
        }

        return $query->where(
            $this->table . '.name',
            (! $partial ? '=' : 'like'),
            ($partial ? '%' : '') . $name . ($partial ? '%' : '')
        );
    }

    public function scopeGetByActive($query, $active = null)
    {
        if ( ! isset($active)) {
            return $query;
        }

        return $query->where($this->table . '.active', (bool)$active);
    }

    public function scopeGetByIncludeInactive($query, $includeInactive = null)
    {
        if ($includeInactive) {
            return $query;
        }

        return $query->where($this->table.'.active', true);
    }

    public function scopeGetById($query, $id)
    {
        if ( ! empty($id)) {
            if (is_array($id)) {
                $query->whereIn('id', $id);
            } else {
                $query->where('id', $id);
            }
        }

        return $query;
    }


    public static function getValidationRulesArray($quizCategoryId = null): array
    {
        $validationRulesArray = [
            'name'   => [
                'required',
                'string',
                'max:100',
                Rule::unique((new QuizCategory)->getTable())->ignore($quizCategoryId),
            ],
            'active' => 'nullable|boolean',
        ];

        return $validationRulesArray;
    }

    public static function getQuizCategoriesSelectionArray(int $filterActive = null): array
    {
        $quizCategories               = QuizCategory::orderBy('name', 'desc')->getByActive($filterActive)->get();
        $quizCategoriesSelectionArray = [];
        foreach ($quizCategories as $nextQuizCategory) {
            $quizCategoriesSelectionArray[$nextQuizCategory->id] = $nextQuizCategory->name;
        }

        return $quizCategoriesSelectionArray;
    }

}

<?php

namespace sergeynilov\QuizzesInit\database\seeders;

use Illuminate\Database\Seeder;
use sergeynilov\QuizzesInit\Library\AppLocale;
use sergeynilov\QuizzesInit\Models\QuizCategory;

class quizCategoriesWithInitData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appLocale = AppLocale::getInstance();

        $quizCategory         = new QuizCategory;
        $quizCategory->id     = 1;
        $quizCategory->active = true;
        $quizCategory
            ->setTranslation('name', $appLocale::APP_LOCALE_ENGLISH, 'Laravel development knowledge')
            ->setTranslation('name', $appLocale::APP_LOCALE_UKRAINIAN, 'Знання розробки Laravel')
            ->setTranslation('name', $appLocale::APP_LOCALE_SPANISH, 'Conocimiento de desarrollo de Laravel')
            ->save(); // /_wwwroot/lar/MS/MS_Votes/__DOCS/Qizzes/Top 91 Laravel Interview Questions and Answers (2023).html

        $quizCategory         = new QuizCategory;
        $quizCategory->id     = 2;
        $quizCategory->active = true;
        $quizCategory
            ->setTranslation('name', $appLocale::APP_LOCALE_ENGLISH, 'Vuejs development knowledge')
            ->setTranslation('name', $appLocale::APP_LOCALE_UKRAINIAN, 'Знання розвитку Vuejs')
            ->setTranslation('name', $appLocale::APP_LOCALE_SPANISH, 'Conocimiento de desarrollo de Vuejs')
            ->save();

        $quizCategory         = new QuizCategory;
        $quizCategory->id     = 3;      // file:///_wwwroot/lar/MS/MS_Votes/__DOCS/Qizzes/Top 50 JavaScript Interview Questions and Answers in 2023.html
        $quizCategory->active = true;
        $quizCategory
            ->setTranslation('name', $appLocale::APP_LOCALE_ENGLISH, 'JavaScript skills')
            ->setTranslation('name', $appLocale::APP_LOCALE_UKRAINIAN, 'Навички JavaScript')
            ->setTranslation('name', $appLocale::APP_LOCALE_SPANISH, 'Habilidades de JavaScript')
            ->save();
    }
}

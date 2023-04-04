<?php

// // packages/sergeynilov/QuizzesInit/src/Providers/QuizzesInitProvider.php
//namespace sergeynilov\QuizzesInit\Providers;
namespace sergeynilov\QuizzesInit\database\seeders;

use Illuminate\Database\Seeder;
use sergeynilov\QuizzesInit\Models\Quiz;
use sergeynilov\QuizzesInit\Library\AppLocale;

class quizzesWithInitData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $appLocale = AppLocale::getInstance();

        $quiz                   = new Quiz;
        $quiz->quiz_category_id = 1; // Laravel development knowledge
        $quiz->points           = 6;
        $quiz->active           = true;
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_ENGLISH, 'What is HTTP middleware ?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_UKRAINIAN, 'Що таке HTTP middleware ?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_SPANISH, 'Qué es el HTTP middleware ?');
        $quiz->save();

//        return;

        $quiz                   = new Quiz;
        $quiz->quiz_category_id = 1; // Laravel development knowledge
        $quiz->points           = 4;
        $quiz->active           = true;
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_ENGLISH, 'What does ORM stand for ?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_UKRAINIAN, 'Що означає ORM ?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_SPANISH, 'Qué significa ORM ?');
        $quiz->save();

        $quiz                   = new Quiz;
        $quiz->quiz_category_id = 1; // Laravel development knowledge
        $quiz->points           = 3;
        $quiz->active           = true;
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_ENGLISH, 'How can you generate URLs ?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_UKRAINIAN, 'Як ви можете створити URL-адреси ?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_SPANISH, 'Cómo se pueden generar direcciones URL ?');
        $quiz->save();

        $quiz                   = new Quiz;
        $quiz->quiz_category_id = 1; // Laravel development knowledge
        $quiz->points           = 4;
        $quiz->active           = true;
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_ENGLISH, 'What is query scope ?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_UKRAINIAN, 'Що таке область запиту?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_SPANISH, 'Qué es el alcance de la consulta?');
        $quiz->save();


        $quiz                   = new Quiz;
        $quiz->quiz_category_id = 1; // Laravel development knowledge
        $quiz->points           = 5;
        $quiz->active           = true;
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_ENGLISH, 'What is namespace in Laravel ?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_UKRAINIAN, 'Що таке простір імен у Laravel?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_SPANISH, 'What is namespace in Laravel ?');
        $quiz->save();


        $quiz                   = new Quiz; // id = 6
        $quiz->quiz_category_id = 2; // Vuejs development knowledge
        $quiz->points           = 7;
        $quiz->active           = true;
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_ENGLISH, 'Explain what the <slot> element is in Vue.js.');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_UKRAINIAN, 'Explain what the <slot> element is in Vue.js.');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_SPANISH, 'Explique qué es el elemento <slot> en Vue.js.');
        $quiz->save();


        $quiz                   = new Quiz; // id = 7
        $quiz->quiz_category_id = 2; // Vuejs development knowledge
        $quiz->points           = 5;
        $quiz->active           = true;
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_ENGLISH, 'What are mixins ?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_UKRAINIAN, 'Що таке міксини (mixins) ?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_SPANISH, 'Qué son los mixins ?');
        $quiz->save();


        $quiz                   = new Quiz; // id = 8
        $quiz->quiz_category_id = 2; // Vuejs development knowledge
        $quiz->points           = 6;
        $quiz->active           = true;
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_ENGLISH, 'What are watchers?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_UKRAINIAN, 'Що таке спостерігачі (watchers) ?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_SPANISH, 'Qué son las observadoras (watchers) ?');
        $quiz->save();



        $quiz                   = new Quiz; // id = 9
        $quiz->quiz_category_id = 3; // JavaScript skills
        $quiz->points           = 4;
        $quiz->active           = true;
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_ENGLISH, 'What is the use of isNaN function?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_UKRAINIAN, 'Яке використання функції isNaN?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_SPANISH, 'Cuál es el uso de la función isNaN?');
        $quiz->save();


        $quiz                   = new Quiz; // id = 10
        $quiz->quiz_category_id = 3; // JavaScript skills
        $quiz->points           = 7;
        $quiz->active           = true;
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_ENGLISH, 'What is ‘this’ keyword in JavaScript?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_UKRAINIAN, 'Що таке ключове слово «this» в JavaScript?');
        $quiz->setTranslation('question', $appLocale::APP_LOCALE_SPANISH, 'Qué es la palabra clave \'this\' en JavaScript?');
        $quiz->save();

    }
}

<?php

namespace sergeynilov\QuizzesInit\database\seeders;


use Illuminate\Database\Seeder;
use sergeynilov\QuizzesInit\Library\AppLocale;
use sergeynilov\QuizzesInit\Models\Settings;

class SettingsWithInitData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appLocale = AppLocale::getInstance();
        $settings       = new Settings;
        $settings->id   = 1;
        $settings->name = 'site_name';
        $settings
            ->setTranslation('value', $appLocale::APP_LOCALE_ENGLISH, 'Quizzes site')
            ->setTranslation('value', $appLocale::APP_LOCALE_UKRAINIAN, 'Вікторини сайт')
            ->setTranslation('value', $appLocale::APP_LOCALE_SPANISH, 'Cuestionarios sitio')
            ->save();

        $settings       = new Settings;
        $settings->id   = 2;
        $settings->name = 'company_name';
        $settings
            ->setTranslation('value', $appLocale::APP_LOCALE_ENGLISH, 'Quiz Developers')
            ->setTranslation('value', $appLocale::APP_LOCALE_UKRAINIAN, 'Розробники вікторини')
            ->setTranslation('value', $appLocale::APP_LOCALE_SPANISH, 'Desarrolladores de cuestionarios')
            ->save();
    }
}

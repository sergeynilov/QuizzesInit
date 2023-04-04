<?php

//namespace App\Library;
namespace sergeynilov\QuizzesInit\Library;

class AppLocale
{
    private static $instance;
    protected static string $currentLocale = 'en';

    public const APP_LOCALE_FLAGS_CONVERTOR = ['en' => 'GB'];
    public const APP_LOCALE_ENGLISH = 'en';
    public const APP_LOCALE_SPANISH = 'es';
    public const APP_LOCALE_UKRAINIAN = 'ua';

    protected static $appLocaleSelectionItems
        = [
            self::APP_LOCALE_ENGLISH   => 'English language',
            self::APP_LOCALE_SPANISH   => 'Lengua española',
            self::APP_LOCALE_UKRAINIAN => 'Українська мова',
        ];

    public static function getAppLocaleSelectionItems(bool $keyReturn = true): array
    {
        $resArray = [];
        foreach (self::$appLocaleSelectionItems as $key => $value) {
            if ($keyReturn) {
                $resArray[] = ['key' => $key, 'label' => $value];
            } else {
                $resArray[$key] = $value;
            }
        }

        return $resArray;
    }

    public static function getAppLocaleLabel(string $appLocale = ''): string
    {
        if ( ! empty(self::$appLocaleSelectionItems[$appLocale])) {
            return self::$appLocaleSelectionItems[$appLocale];
        }

        return self::$appLocaleSelectionItems[self::APP_LOCALE_ENGLISH];
    }

    private function __construct()
    {
        // protected constructor to prevent object creation
    }

    // as Country flag in Emoji can be different from locale - need find and use  it
    public static function getLocaleCountryFlag(string $locale): string
    {
        foreach (self::APP_LOCALE_FLAGS_CONVERTOR as $key => $flag) {
            if ($key === $locale) {
                return $flag;
            }
        }

        return $locale;
    }

    public static function getLocaleImageUrlByLocale(string $locale): string
    {
        foreach (self::getAppLocaleSelectionItems(false) as $nextLocale => $label) {
            if ($locale === $nextLocale) {
                return '/images/langs/' . $locale . '.png';
            }
        }

        return '';
    }

    public static function getCurrentLocaleImageUrl(): string
    {
        $currentLocale = app()->getLocale();
        foreach (self::getAppLocaleSelectionItems(false) as $locale => $label) {
            if ($currentLocale === $locale) {
                return '/images/langs/' . $locale . '.png';
            }
        }

        return '';
    }

    public static function getLocateImages(bool $returnImageSource = true): array
    {
        $retArray = [];
        foreach (self::getAppLocaleSelectionItems(false) as $locale => $label) {
            if ($returnImageSource) {
                $retArray[$locale] = '<img src="/images/langs/' . $locale . '.png" title="' . $label . '">';
            }
        }

        return $retArray;
    }

    public static function getCurrentLocale()
    {
        self::$currentLocale = app()->getLocale();
        return self::$currentLocale;
    }

    public static function getInstance(string $currentLocale = '')
    {
        if (self::$instance === null) {
//            \Log::info(' -1 new CREATED AppLocale::');
//            \Log::info(varDump($currentLocale, ' -1 $currentLocale::'));
            self::$instance = new AppLocale();
        }
//        \Log::info(varDump(self::$instance, ' -1 AFTER getInstances self::$instance::'));

        return self::$instance;
    }
}

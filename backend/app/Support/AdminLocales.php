<?php

namespace App\Support;

final class AdminLocales
{
    public const string COOKIE = 'admin_locale';

    /** @var list<string> */
    public const array ALLOWED = ['en', 'zh_TW', 'ja', 'ko'];

    public static function htmlLang(string $locale): string
    {
        return match ($locale) {
            'zh_TW' => 'zh-Hant',
            'ja' => 'ja',
            'ko' => 'ko',
            default => 'en',
        };
    }

    /** BCP 47 tag for `Intl` / `Date.toLocaleString` (matches admin language, not browser OS). */
    public static function intlLocale(string $locale): string
    {
        return match ($locale) {
            'zh_TW' => 'zh-TW',
            'ja' => 'ja-JP',
            'ko' => 'ko-KR',
            default => 'en-AU',
        };
    }
}

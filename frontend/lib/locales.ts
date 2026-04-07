export const SUPPORTED_LOCALES = ["en", "zh-TW", "ja", "ko"] as const;

export type AppLocale = (typeof SUPPORTED_LOCALES)[number];

export const DEFAULT_LOCALE: AppLocale = "en";

export const LOCALE_LABELS: Record<AppLocale, string> = {
  en: "English",
  "zh-TW": "繁體中文",
  ja: "日本語",
  ko: "한국어",
};

export const LOCALE_STORAGE_KEY = "edward_store_locale";

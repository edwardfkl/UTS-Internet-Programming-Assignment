import { apiBase } from "@/lib/api";
import type { AppLocale } from "@/lib/locales";

/** Laravel `in:en,zh_TW,ja,ko` (Traditional Chinese uses underscore). */
export function localeToApi(locale: AppLocale): string {
  return locale === "zh-TW" ? "zh_TW" : locale;
}

export function localeFromApi(raw: string): AppLocale | null {
  if (raw === "zh_TW") return "zh-TW";
  if (raw === "en" || raw === "ja" || raw === "ko") return raw;
  return null;
}

export async function fetchServerLocale(): Promise<AppLocale | null> {
  const res = await fetch(`${apiBase()}/api/locale`, {
    credentials: "include",
    headers: { Accept: "application/json" },
  });
  if (!res.ok) return null;
  const data = (await res.json()) as { locale?: string | null };
  const raw = data.locale;
  if (typeof raw !== "string") return null;
  return localeFromApi(raw);
}

export async function pushServerLocale(locale: AppLocale): Promise<void> {
  await fetch(`${apiBase()}/api/locale`, {
    method: "POST",
    credentials: "include",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ locale: localeToApi(locale) }),
  });
}

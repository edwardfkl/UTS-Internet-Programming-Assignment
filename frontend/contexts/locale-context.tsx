"use client";

import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
} from "react";
import type { MessageTree } from "@/lib/i18n-resolve";
import { resolveMessage } from "@/lib/i18n-resolve";
import {
  fetchServerLocale,
  pushServerLocale,
} from "@/lib/localeSync";
import {
  DEFAULT_LOCALE,
  LOCALE_STORAGE_KEY,
  type AppLocale,
} from "@/lib/locales";
import en from "@/messages/en.json";
import ja from "@/messages/ja.json";
import ko from "@/messages/ko.json";
import zhTW from "@/messages/zh-TW.json";

const bundles: Record<AppLocale, MessageTree> = {
  en: en as MessageTree,
  "zh-TW": zhTW as MessageTree,
  ja: ja as MessageTree,
  ko: ko as MessageTree,
};

const HTML_LANG: Record<AppLocale, string> = {
  en: "en",
  "zh-TW": "zh-Hant",
  ja: "ja",
  ko: "ko",
};

function readStoredLocale(): AppLocale {
  if (typeof window === "undefined") return DEFAULT_LOCALE;
  const raw = window.localStorage.getItem(LOCALE_STORAGE_KEY);
  if (raw === "en" || raw === "zh-TW" || raw === "ja" || raw === "ko") {
    return raw;
  }
  return DEFAULT_LOCALE;
}

type LocaleContextValue = {
  locale: AppLocale;
  setLocale: (next: AppLocale) => void;
  t: (path: string) => string;
  tf: (path: string, vars: Record<string, string | number>) => string;
};

const LocaleContext = createContext<LocaleContextValue | null>(null);

/** Avoid duplicate GET/POST when React Strict Mode double-mounts in development. */
let storefrontLocaleBootstrapRan = false;

export function LocaleProvider({ children }: { children: React.ReactNode }) {
  const [locale, setLocaleState] = useState<AppLocale>(() =>
    typeof window === "undefined" ? DEFAULT_LOCALE : readStoredLocale(),
  );

  useEffect(() => {
    if (typeof window === "undefined") return;
    if (storefrontLocaleBootstrapRan) return;
    storefrontLocaleBootstrapRan = true;
    void (async () => {
      const serverLocale = await fetchServerLocale();
      if (serverLocale) {
        setLocaleState(serverLocale);
        window.localStorage.setItem(LOCALE_STORAGE_KEY, serverLocale);
        return;
      }
      void pushServerLocale(readStoredLocale());
    })();
  }, []);

  useEffect(() => {
    if (typeof document === "undefined") return;
    document.documentElement.lang = HTML_LANG[locale];
  }, [locale]);

  const setLocale = useCallback((next: AppLocale) => {
    setLocaleState((prev) => {
      if (prev === next) return prev;
      if (typeof window !== "undefined") {
        window.localStorage.setItem(LOCALE_STORAGE_KEY, next);
      }
      void pushServerLocale(next);
      return next;
    });
  }, []);

  const messages = bundles[locale];

  const t = useCallback(
    (path: string) => resolveMessage(messages, path),
    [messages],
  );

  const tf = useCallback(
    (path: string, vars: Record<string, string | number>) => {
      let s = resolveMessage(messages, path);
      for (const [k, v] of Object.entries(vars)) {
        s = s.split(`{${k}}`).join(String(v));
      }
      return s;
    },
    [messages],
  );

  const value = useMemo(
    () => ({ locale, setLocale, t, tf }),
    [locale, setLocale, t, tf],
  );

  return (
    <LocaleContext.Provider value={value}>{children}</LocaleContext.Provider>
  );
}

export function useLocale(): LocaleContextValue {
  const ctx = useContext(LocaleContext);
  if (!ctx) {
    throw new Error("useLocale must be used within LocaleProvider");
  }
  return ctx;
}

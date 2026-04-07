"use client";

import { useCallback, useEffect, useId, useRef, useState } from "react";
import { useLocale } from "@/contexts/locale-context";
import {
  LOCALE_LABELS,
  SUPPORTED_LOCALES,
  type AppLocale,
} from "@/lib/locales";

function GlobeIcon({ className }: { className?: string }) {
  return (
    <svg
      className={className}
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth={1.5}
      aria-hidden
    >
      <path
        strokeLinecap="round"
        strokeLinejoin="round"
        d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253M12 3v18"
      />
    </svg>
  );
}

/** Globe button + locale dropdown (same interaction pattern as UserAvatarMenu). */
export function LanguageMenu() {
  const { locale, setLocale, t } = useLocale();
  const [open, setOpen] = useState(false);
  const rootRef = useRef<HTMLDivElement>(null);
  const menuId = useId();

  const close = useCallback(() => setOpen(false), []);

  useEffect(() => {
    if (!open) return;
    function onPointerDown(e: MouseEvent): void {
      const el = rootRef.current;
      if (!el || el.contains(e.target as Node)) return;
      close();
    }
    function onKey(e: KeyboardEvent): void {
      if (e.key === "Escape") close();
    }
    document.addEventListener("mousedown", onPointerDown);
    document.addEventListener("keydown", onKey);
    return () => {
      document.removeEventListener("mousedown", onPointerDown);
      document.removeEventListener("keydown", onKey);
    };
  }, [open, close]);

  return (
    <div ref={rootRef} className="relative z-20">
      <button
        type="button"
        className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-stone-200 bg-white text-stone-700 shadow-sm ring-offset-2 outline-none hover:bg-stone-50 focus-visible:ring-2 focus-visible:ring-amber-700"
        aria-expanded={open}
        aria-haspopup="menu"
        aria-controls={open ? menuId : undefined}
        onClick={() => setOpen((v) => !v)}
        title={t("lang.title")}
      >
        <span className="sr-only">{t("common.openLanguageMenu")}</span>
        <GlobeIcon className="h-5 w-5" />
      </button>

      {open ? (
        <div
          id={menuId}
          role="menu"
          aria-label={t("lang.title")}
          className="absolute right-0 mt-2 min-w-[10.5rem] rounded-xl border border-stone-200 bg-white py-1 shadow-lg"
        >
          {SUPPORTED_LOCALES.map((code) => (
            <button
              key={code}
              type="button"
              role="menuitemradio"
              aria-checked={locale === code}
              className={`w-full px-4 py-2.5 text-left text-sm ${
                locale === code
                  ? "bg-amber-50 font-medium text-amber-950"
                  : "text-stone-800 hover:bg-stone-50"
              }`}
              onClick={() => {
                setLocale(code as AppLocale);
                close();
              }}
            >
              {LOCALE_LABELS[code]}
            </button>
          ))}
        </div>
      ) : null}
    </div>
  );
}

"use client";

import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { Suspense, useEffect, useState } from "react";
import { ShopHeader } from "@/components/ShopHeader";
import { useAuth } from "@/contexts/auth-context";
import { useLocale } from "@/contexts/locale-context";

function safeRedirect(path: string | null): string {
  if (!path || !path.startsWith("/")) return "/";
  if (path.startsWith("//")) return "/";
  return path;
}

function LoginContent() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const redirectTo = safeRedirect(searchParams.get("redirect"));
  const { t } = useLocale();
  const { login, user, ready } = useAuth();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);

  useEffect(() => {
    if (ready && user) router.replace(redirectTo);
  }, [ready, user, router, redirectTo]);

  async function onSubmit(e: React.FormEvent): Promise<void> {
    e.preventDefault();
    setError(null);
    setBusy(true);
    try {
      await login(email, password);
      router.replace(redirectTo);
    } catch (err) {
      setError(err instanceof Error ? err.message : t("common.loginFailed"));
    } finally {
      setBusy(false);
    }
  }

  if (ready && user) {
    return (
      <div className="min-h-full">
        <ShopHeader />
        <p className="p-8 text-center text-sm text-stone-500">{t("common.redirecting")}</p>
      </div>
    );
  }

  return (
    <div className="min-h-full">
      <ShopHeader />
      <main className="mx-auto max-w-md px-4 py-12 sm:px-6">
        <h1 className="font-display text-2xl font-semibold text-stone-900">{t("login.title")}</h1>
        <p className="mt-2 text-sm text-stone-600">
          {t("login.subtitle")}
        </p>
        <form
          onSubmit={(e) => void onSubmit(e)}
          onKeyDown={(e: React.KeyboardEvent<HTMLFormElement>) => {
            if (e.key !== "Enter" || busy) return;
            if (e.nativeEvent.isComposing) return;
            if (!(e.target instanceof HTMLInputElement)) return;
            e.preventDefault();
            e.currentTarget.requestSubmit();
          }}
          className="mt-8 space-y-4"
        >
          <div>
            <label htmlFor="email" className="block text-sm font-medium text-stone-700">
              {t("common.email")}
            </label>
            <input
              id="email"
              type="email"
              autoComplete="email"
              required
              value={email}
              onChange={(ev) => setEmail(ev.target.value)}
              className="mt-1 w-full rounded-lg border border-stone-200 px-3 py-2 text-stone-900 shadow-sm"
            />
          </div>
          <div>
            <label htmlFor="password" className="block text-sm font-medium text-stone-700">
              {t("common.password")}
            </label>
            <input
              id="password"
              type="password"
              autoComplete="current-password"
              required
              value={password}
              onChange={(ev) => setPassword(ev.target.value)}
              className="mt-1 w-full rounded-lg border border-stone-200 px-3 py-2 text-stone-900 shadow-sm"
            />
          </div>
          {error ? (
            <p className="text-sm text-red-800" role="alert">
              {error}
            </p>
          ) : null}
          <button
            type="submit"
            disabled={busy}
            className="w-full rounded-lg bg-amber-800 py-2.5 text-sm font-medium text-white hover:bg-amber-900 disabled:opacity-50"
          >
            {busy ? t("login.signingIn") : t("login.signIn")}
          </button>
        </form>
        <p className="mt-6 text-center text-sm text-stone-600">
          {t("login.noAccount")}{" "}
          <Link
            href={redirectTo !== "/" ? `/register?redirect=${encodeURIComponent(redirectTo)}` : "/register"}
            className="font-medium text-amber-900 hover:underline"
          >
            {t("login.register")}
          </Link>
        </p>
      </main>
    </div>
  );
}

function LoginSuspenseFallback() {
  const { t } = useLocale();
  return (
    <div className="min-h-full">
      <ShopHeader />
      <p className="p-8 text-center text-sm text-stone-500">{t("common.loading")}</p>
    </div>
  );
}

export default function LoginPage() {
  return (
    <Suspense fallback={<LoginSuspenseFallback />}>
      <LoginContent />
    </Suspense>
  );
}

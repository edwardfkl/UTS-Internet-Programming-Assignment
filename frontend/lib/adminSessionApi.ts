import { apiBase } from "./api";
import { getAuthToken } from "./authToken";

/**
 * After SPA login, creates the Laravel `web` session so /admin recognises the same user.
 * Uses `credentials: "include"` so the session cookie is stored for the API host.
 */
export async function syncAdminWebSession(): Promise<void> {
  const t = getAuthToken();
  if (!t) return;

  const res = await fetch(`${apiBase()}/api/admin/web-session`, {
    method: "POST",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${t}`,
    },
    credentials: "include",
  });

  if (!res.ok) {
    let msg = `Could not sync admin session (HTTP ${res.status})`;
    try {
      const body = (await res.json()) as { message?: string };
      if (body.message) msg = body.message;
    } catch {
      /* ignore */
    }
    throw new Error(msg);
  }
}

/** Clear the Blade admin session when logging out of the SPA. */
export async function clearAdminWebSession(): Promise<void> {
  const t = getAuthToken();
  if (!t) return;

  try {
    await fetch(`${apiBase()}/api/admin/web-session`, {
      method: "DELETE",
      headers: {
        Accept: "application/json",
        Authorization: `Bearer ${t}`,
      },
      credentials: "include",
    });
  } catch {
    /* offline or CORS */
  }
}

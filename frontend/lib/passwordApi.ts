import { apiBase } from "./api";
import { getAuthToken } from "./authToken";

export async function changePassword(input: {
  currentPassword: string;
  password: string;
  passwordConfirmation: string;
}): Promise<void> {
  const t = getAuthToken();
  if (!t) throw new Error("Not signed in");
  const res = await fetch(`${apiBase()}/api/password`, {
    method: "PATCH",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
      Authorization: `Bearer ${t}`,
    },
    body: JSON.stringify({
      current_password: input.currentPassword,
      password: input.password,
      password_confirmation: input.passwordConfirmation,
    }),
  });
  if (!res.ok) {
    let msg = `Could not change password (HTTP ${res.status})`;
    try {
      const body = (await res.json()) as {
        message?: string;
        errors?: Record<string, string[]>;
      };
      const first = body.errors && Object.values(body.errors)[0]?.[0];
      if (first) msg = first;
      else if (body.message) msg = body.message;
    } catch {
      /* ignore */
    }
    throw new Error(msg);
  }
}

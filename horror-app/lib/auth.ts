import { API_BASE } from "./api";

export async function requireAuth() {
  const res = await fetch(`${API_BASE}/me.php`, {
    credentials: "include",
    cache: "no-cache",
  });

  const data = await res.json();

  if (!data.authenticated) {
    return null;
  }

  return data;
}

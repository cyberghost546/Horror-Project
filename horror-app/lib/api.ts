export const API_BASE =
  process.env.NEXT_PUBLIC_API_BASE_URL || "http://localhost/my-horror-project/horror_blog/api";

async function jsonGet(path: string) {
  const res = await fetch(`${API_BASE}${path}`, {
    credentials: "include",
  });

  if (!res.ok) {
    throw new Error(`Request failed ${res.status}`);
  }

  return res.json();
}

async function jsonPost(path: string, body: unknown) {
  const res = await fetch(`${API_BASE}${path}`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    credentials: "include",
    body: JSON.stringify(body),
  });

  if (!res.ok) {
    throw new Error(`Request failed ${res.status}`);
  }

  return res.json();
}

export async function fetchStories(limit = 20) {
  const data = await jsonGet(`/stories.php?limit=${limit}`);
  return data.data;
}

export async function fetchStory(id: string) {
  const data = await jsonGet(`/story.php?id=${id}`);
  return data.data;
}

export async function apiLogin(email: string, password: string) {
  const data = await jsonPost("/login.php", { email, password });
  return data.data;
}

export async function fetchMe() {
  const data = await jsonGet("/me.php");
  return data;
}

export async function apiLogout() {
  const data = await jsonGet("/logout.php");
  return data;
}

export async function submitStory(title: string, category: string, content: string) {
  const res = await fetch(`${API_BASE}/submit_story.php`, {
    method: "POST",
    credentials: "include",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ title, category, content }),
  });

  return res.json();
}

export async function checkAdmin() {
  const res = await fetch(`${API_BASE}/admin_check.php`, {
    credentials: "include",
  });

  return res.json();
}

export async function getLikes(storyId: string) {
  const res = await fetch(`${API_BASE}/likes_get.php?story_id=${storyId}`, {
    credentials: "include",
  });
  return res.json();
}

export async function toggleLike(storyId: string) {
  const res = await fetch(`${API_BASE}/likes_toggle.php`, {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ story_id: storyId }),
  });
  return res.json();
}

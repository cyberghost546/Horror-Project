import { requireAuth } from "../../lib/auth";

export default async function ProfilePage() {
  const me = await requireAuth();

  if (!me) {
    return <p>You are not logged in.</p>;
  }

  const user = me.data;

  return (
    <div>
      <h1 style={{ fontSize: "1.8rem", fontWeight: 700 }}>
        Your Profile
      </h1>

      <p style={{ marginTop: "1rem" }}>
        <strong>Name:</strong> {user.display_name}
      </p>

      <p>
        <strong>Role:</strong> {user.role}
      </p>

      <div style={{ marginTop: "2rem" }}>
        <a href="/stories" style={{ color: "#f33" }}>
          View Your Stories
        </a>
      </div>

      <div style={{ marginTop: "0.5rem" }}>
        <a href="/submit" style={{ color: "#f33" }}>
          Submit New Story
        </a>
      </div>
    </div>
  );
}

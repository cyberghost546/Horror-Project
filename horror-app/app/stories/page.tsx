import Link from "next/link";
import { fetchStories } from "../../lib/api";

type Story = {
  id: number;
  title: string;
  slug: string;
  category: string;
  excerpt: string;
  created_at: string;
  likes: number;
  author: string;
};

export default async function StoriesPage() {
  let stories: Story[] = [];

  try {
    stories = await fetchStories(50);
  } catch (err) {
    console.error("Failed to load stories", err);
  }

  return (
    <div>
      <h1
        style={{
          fontSize: "1.75rem",
          fontWeight: 700,
          marginBottom: "1rem",
        }}
      >
        All Stories
      </h1>

      {stories.length === 0 && <p>No stories found.</p>}

      <ul style={{ listStyle: "none", padding: 0, margin: 0 }}>
        {stories.map((story) => (
          <li
            key={story.id}
            style={{
              padding: "0.75rem 0",
              borderBottom: "1px solid #27272a",
            }}
          >
            <h2
              style={{
                fontSize: "1.1rem",
                fontWeight: 600,
                margin: 0,
                marginBottom: "0.25rem",
              }}
            >
              <Link href={`/stories/${story.id}`}>{story.title}</Link>
            </h2>
            <p
              style={{
                fontSize: "0.8rem",
                opacity: 0.75,
                margin: 0,
                marginBottom: "0.25rem",
              }}
            >
              {story.category} · by {story.author} ·{" "}
              {new Date(story.created_at).toLocaleDateString()}
            </p>
            <p
              style={{
                margin: 0,
                fontSize: "0.9rem",
                color: "#e5e5e5",
              }}
            >
              {story.excerpt}
            </p>
          </li>
        ))}
      </ul>
    </div>
  );
}

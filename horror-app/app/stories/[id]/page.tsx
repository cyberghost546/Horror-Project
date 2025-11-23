import { fetchStory } from "../../../lib/api";
import Comments from "./Comments";

type Story = {
    id: number;
    title: string;
    slug: string;
    content: string;
    category: string;
    created_at: string;
    likes: number;
    author: string;
};

type StoryPageProps = {
    params: {
        id: string;
    };
};

export default async function StoryPage({ params }: StoryPageProps) {
    let story: Story;

    try {
        story = await fetchStory(params.id);
    } catch (err) {
        console.error("Failed to load story", err);
        return <p>Could not load story.</p>;
    }



    return (
        <article>
            <header style={{ marginBottom: "1.5rem" }}>
                <h1
                    style={{
                        fontSize: "2rem",
                        fontWeight: 700,
                        marginBottom: "0.5rem",
                    }}
                >
                    {story.title}
                </h1>
                <p
                    style={{
                        fontSize: "0.85rem",
                        opacity: 0.8,
                    }}
                >
                    {story.category} · by {story.author} ·{" "}
                    {new Date(story.created_at).toLocaleDateString()} · Likes:{" "}
                    {story.likes}
                </p>
            </header>


            <div
                {story.image && (
                    <img
                        src={`http://localhost/horror_blog/${story.image}`}
                        alt="Story Image"
                        style={{
                            width: "100%",
                            maxHeight: "400px",
                            objectFit: "cover",
                            borderRadius: "8px",
                            marginBottom: "1.5rem",
                            border: "1px solid #7f1d1d",
                        }}
                    />
                )}

            >

                {story.content}
            </div>
            <div style={{ marginTop: "2rem" }}>
                <Comments storyId={params.id} />
            </div>
        </article>

    );
}

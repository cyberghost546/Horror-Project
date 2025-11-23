"use client";

import { useEffect, useState } from "react";

export default function Comments({ storyId }: { storyId: string }) {
  const [comments, setComments] = useState([]);
  const [text, setText] = useState("");

  async function loadComments() {
    const res = await fetch(
      `http://localhost/project_blog/horror_blog/api/comments_list.php?story_id=${storyId}`
    );
    const data = await res.json();
    if (data.success) setComments(data.comments);
  }

  async function postComment() {
    const res = await fetch(`http://localhost/project_blog/horror_blog/api/comments_add.php`, {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ story_id: storyId, content: text }),
    });

    const data = await res.json();
    if (data.success) {
      setText("");
      loadComments();
    }
  }

  useEffect(() => {
    loadComments();
  }, []);

  return (
    <div>
      <h2 style={{ marginTop: "2rem", marginBottom: "1rem" }}>Comments</h2>

      <div style={{ marginBottom: "1rem" }}>
        <textarea
          rows={3}
          value={text}
          onChange={(e) => setText(e.target.value)}
          style={{
            width: "100%",
            background: "#111",
            color: "white",
            border: "1px solid #333",
            padding: "0.5rem",
          }}
        ></textarea>

        <button
          onClick={postComment}
          style={{
            padding: "0.5rem 1rem",
            background: "#7f1d1d",
            border: "none",
            marginTop: "0.5rem",
            borderRadius: "4px",
            cursor: "pointer",
          }}
        >
          Post Comment
        </button>
      </div>

      {comments.map((c: any) => (
        <div
          key={c.id}
          style={{
            padding: "0.5rem",
            borderBottom: "1px solid #333",
            marginBottom: "0.7rem",
          }}
        >
          <strong>{c.display_name}</strong>
          <p>{c.content}</p>
          <span style={{ opacity: 0.6, fontSize: "0.8rem" }}>
            {c.created_at}
          </span>
        </div>
      ))}
    </div>
  );
}

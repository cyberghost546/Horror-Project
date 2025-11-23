"use client";

import { useState, FormEvent } from "react";
import { submitStory } from "../../lib/api";
import { useRouter } from "next/navigation";
import { redirect } from "next/navigation";
import { requireAuth } from "../../lib/auth";

export default async function ProtectedSubmitPage() {
  const me = await requireAuth();

  if (!me) {
    redirect("/login");
  }

  return <SubmitForm />;
}

function SubmitForm() {
  const router = useRouter();

  const [title, setTitle] = useState("");
  const [category, setCategory] = useState("");
  const [content, setContent] = useState("");
  const [message, setMessage] = useState("");

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setMessage("");

    const res = await submitStory(title, category, content);

    if (!res.success) {
      setMessage(res.error || "Something went wrong");
      return;
    }

    setMessage("Story submitted!");

    setTimeout(() => router.push("/stories"), 1000);
  }

  return (
    <div
      style={{
        maxWidth: "600px",
        margin: "0 auto",
        padding: "2rem",
      }}
    >
      <h1
        style={{
          fontSize: "1.8rem",
          fontWeight: 700,
          marginBottom: "1rem",
        }}
      >
        Submit a New Story
      </h1>

      {message && (
        <p
          style={{
            backgroundColor: "#222",
            padding: "0.5rem",
            borderRadius: "5px",
            border: "1px solid #7f1d1d",
            marginBottom: "1rem",
          }}
        >
          {message}
        </p>
      )}

      <form
        onSubmit={handleSubmit}
        style={{
          display: "flex",
          flexDirection: "column",
          gap: "1rem",
        }}
      >
        <div>
          <label>Title</label>
          <input
            required
            value={title}
            onChange={(e) => setTitle(e.target.value)}
            style={{
              width: "100%",
              padding: "0.5rem",
              background: "#111",
              marginTop: "0.3rem",
              border: "1px solid #333",
              color: "white",
            }}
          />
        </div>

        <div>
          <label>Category</label>
          <input
            value={category}
            onChange={(e) => setCategory(e.target.value)}
            style={{
              width: "100%",
              padding: "0.5rem",
              background: "#111",
              marginTop: "0.3rem",
              border: "1px solid #333",
              color: "white",
            }}
          />
        </div>

        <div>
          <label>Content</label>
          <textarea
            required
            rows={10}
            value={content}
            onChange={(e) => setContent(e.target.value)}
            style={{
              width: "100%",
              padding: "0.5rem",
              background: "#111",
              marginTop: "0.3rem",
              border: "1px solid #333",
              color: "white",
            }}
          ></textarea>
        </div>

        <button
          type="submit"
          style={{
            background: "#7f1d1d",
            color: "white",
            padding: "0.7rem",
            border: "none",
            borderRadius: "5px",
            cursor: "pointer",
          }}
        >
          Submit Story
        </button>
      </form>
    </div>
  );
}

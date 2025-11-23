"use client";

import { useEffect, useState } from "react";
import { getLikes, toggleLike } from "../../../lib/api";

export default function LikeButton({ storyId }: { storyId: string }) {
  const [likes, setLikes] = useState(0);
  const [liked, setLiked] = useState(false);

  async function load() {
    const data = await getLikes(storyId);
    if (data.success) {
      setLikes(data.likes);
      setLiked(data.liked);
    }
  }

  async function handleClick() {
    const data = await toggleLike(storyId);
    if (data.success) {
      load();
    }
  }

  useEffect(() => {
    load();
  }, []);

  return (
    <button
      onClick={handleClick}
      style={{
        padding: "0.5rem 1rem",
        background: liked ? "#7f1d1d" : "#333",
        color: "white",
        borderRadius: "5px",
        cursor: "pointer",
        border: "none",
      }}
    >
      ❤️ {likes} {liked ? "Unlike" : "Like"}
    </button>
  );
}

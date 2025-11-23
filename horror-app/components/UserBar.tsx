"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { fetchMe, apiLogout } from "../lib/api";

type MeResponse = {
  authenticated: boolean;
  data?: {
    id: number;
    display_name: string;
    role: string;
  };
};

export default function UserBar() {
  const [me, setMe] = useState<MeResponse | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function loadMe() {
      try {
        const result = await fetchMe();
        setMe(result as MeResponse);
      } catch (err) {
        setMe({ authenticated: false });
      } finally {
        setLoading(false);
      }
    }

    loadMe();
  }, []);

  async function handleLogout() {
    try {
      await apiLogout();
      setMe({ authenticated: false });
    } catch (err) {
      // ignore for now
    }
  }

  if (loading) {
    return (
      <span style={{ fontSize: "0.8rem", opacity: 0.7 }}>
        Checking session...
      </span>
    );
  }

  if (!me || !me.authenticated) {
    return (
      <span style={{ fontSize: "0.85rem" }}>
        Not logged in.{" "}
        <Link href="/login" style={{ textDecoration: "underline" }}>
          Login
        </Link>
      </span>
    );
  }

  const name =
    me.data && me.data.display_name && me.data.display_name.trim() !== ""
      ? me.data.display_name
      : "User";

  return (
    <span style={{ fontSize: "0.85rem" }}>
      Logged in as {name}{" "}
      <button
        type="button"
        onClick={handleLogout}
        style={{
          marginLeft: "0.5rem",
          padding: "0.1rem 0.4rem",
          fontSize: "0.75rem",
          borderRadius: "4px",
          border: "1px solid #7f1d1d",
          backgroundColor: "#111",
          color: "white",
          cursor: "pointer",
        }}
      >
        Logout
      </button>
    </span>
  );
}

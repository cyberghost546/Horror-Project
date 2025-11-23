"use client";

import { useState, FormEvent } from "react";
import { apiLogin } from "../../lib/api";
import { useRouter } from "next/navigation";

export default function LoginPage() {
  const router = useRouter();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError("");

    try {
      await apiLogin(email, password);
      router.push("/"); // redirect to homepage
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    } catch (err: unknown) {
      setError("Invalid email or password");
    }
  }

  return (
    <div
      style={{
        maxWidth: "400px",
        margin: "0 auto",
        padding: "2rem",
        backgroundColor: "#111",
        borderRadius: "8px",
        border: "1px solid #7f1d1d",
      }}
    >
      <h1
        style={{
          fontSize: "1.5rem",
          marginBottom: "1rem",
          fontWeight: 700,
          textAlign: "center",
        }}
      >
        Login
      </h1>

      {error && (
        <p
          style={{
            backgroundColor: "#7f1d1d",
            padding: "0.5rem",
            borderRadius: "4px",
            marginBottom: "1rem",
            color: "white",
          }}
        >
          {error}
        </p>
      )}

      <form onSubmit={handleSubmit} style={{ display: "flex", flexDirection: "column", gap: "1rem" }}>
        <div>
          <label style={{ fontSize: "0.9rem", display: "block", marginBottom: "0.3rem" }}>
            Email
          </label>
          <input
            type="email"
            required
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            style={{
              width: "100%",
              padding: "0.5rem",
              borderRadius: "4px",
              border: "1px solid #333",
              backgroundColor: "#222",
              color: "white",
            }}
          />
        </div>

        <div>
          <label style={{ fontSize: "0.9rem", display: "block", marginBottom: "0.3rem" }}>
            Password
          </label>
          <input
            type="password"
            required
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            style={{
              width: "100%",
              padding: "0.5rem",
              borderRadius: "4px",
              border: "1px solid #333",
              backgroundColor: "#222",
              color: "white",
            }}
          />
        </div>

        <button
          type="submit"
          style={{
            padding: "0.7rem",
            marginTop: "0.5rem",
            backgroundColor: "#7f1d1d",
            border: "none",
            borderRadius: "4px",
            cursor: "pointer",
            fontWeight: 600,
          }}
        >
          Sign In
        </button>
      </form>
    </div>
  );
}

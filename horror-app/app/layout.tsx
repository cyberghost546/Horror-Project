import "./globals.css";
import Link from "next/link";
import UserBar from "../components/UserBar";

export const metadata = {
  title: "Horror Stories",
  description: "Frontend for your horror blog",
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en">
      <body className="bg-horrorDark text-white min-h-screen">
        <header className="border-b border-horrorRed p-4 flex items-center justify-between bg-black">
          <div className="flex items-center gap-6">
            <h1 className="font-bold text-lg">Horror Stories</h1>
            <nav className="flex gap-4 text-sm">
              <Link href="/">Home</Link>
              <Link href="/stories">Stories</Link>
              <Link href="/submit">Submit</Link>
              <Link href="/profile">Profile</Link>
              <Link href="/login">Login</Link>
            </nav>
          </div>
          <UserBar />
        </header>

        <main className="max-w-3xl mx-auto p-6">{children}</main>
      </body>
    </html>
  );
}

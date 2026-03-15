"use client";

import { useEffect, useState } from "react";
import api from "@/lib/api";

export default function Dashboard() {
  const [workspaces, setWorkspaces] = useState<any[]>([]);

  useEffect(() => {
    api.get("/me").then((res) => {
      setWorkspaces(res.data);
    });
  }, []);

  return (
    <div className="min-h-screen bg-black text-white p-8">
      <h1 className="text-3xl mb-6">Your Workspaces</h1>

      <div className="grid gap-4">
        {workspaces.map((ws) => (
          <div key={ws.id} className="p-4 bg-zinc-900 rounded-lg">
            {ws.name}
          </div>
        ))}
      </div>
    </div>
  );
}
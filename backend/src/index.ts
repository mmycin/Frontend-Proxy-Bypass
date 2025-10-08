import express from "express"
import type { Request, Response } from "express";
import { authorizeWithApiKey } from "./middleware/auth-header.js";

const app = express();
const PORT = process.env.PORT || 3000;

// Enable JSON body parsing
app.use(express.json());

// --- Routes ---

// Public route (no key required)
app.get("/public", (req: Request, res: Response) => {
    res.json({ message: "This is a public route." });
});

// Protected route (API key required)
app.post("/protected", authorizeWithApiKey, (req: Request, res: Response) => {
    const body = req.body;
    console.log("Received data:", body);

    res.json({
        message:
            "✅ Success! You accessed the protected route with a valid API Key.",
        received: body,
    });
});

// Start server
app.listen(PORT, () => {
    console.log(`🔥 Server running on http://localhost:${PORT}`);
});

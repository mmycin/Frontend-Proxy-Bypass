import type { Request, Response, NextFunction } from "express";

// Read API keys from environment variables and split them into an array
const VALID_API_KEYS: string = process.env.API_KEY || "";

/**
 * Middleware to enforce Bearer API Key authorization.
 * Checks for the header 'Authorization: Bearer <API_KEY>'.
 */
export const authorizeWithApiKey = (
    req: Request,
    res: Response,
    next: NextFunction
) => {
    // 1. Get the Authorization header
    const authHeader = req.headers.authorization;

    // Check if the header is missing
    if (!authHeader) {
        return res
            .status(401)
            .json({ message: "Authorization header is missing." });
    }

    // The header format must be "Bearer <API_KEY>"
    const parts = authHeader.split(" ");

    // 2. Check for the correct Bearer format
    if (parts.length !== 2 || parts[0].toLowerCase() !== "bearer") {
        return res.status(401).json({
            message:
                'Invalid Authorization header format. Format must be "Bearer <API_KEY>".',
        });
    }

    // Extract the API Key
    const apiKey = parts[1];

    // 3. Validate the API Key
    if (apiKey === VALID_API_KEYS) {
        // Key is valid, grant access
        // Optional: Log the key or attach client info to req for further processing
        next();
    } else {
        // Key is invalid, deny access
        return res.status(403).json({ message: "Forbidden: Invalid API Key." });
    }
};

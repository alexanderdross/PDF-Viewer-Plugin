/**
 * Webhook utilities
 */

/**
 * Generate a webhook signature
 * Uses HMAC-SHA256 for signing
 */
export async function generateWebhookSignature(
  payload: string,
  secret: string
): Promise<string> {
  const encoder = new TextEncoder();
  const keyData = encoder.encode(secret);
  const payloadData = encoder.encode(payload);

  const key = await crypto.subtle.importKey(
    'raw',
    keyData,
    { name: 'HMAC', hash: 'SHA-256' },
    false,
    ['sign']
  );

  const signature = await crypto.subtle.sign('HMAC', key, payloadData);
  const hashArray = Array.from(new Uint8Array(signature));
  const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');

  return `sha256=${hashHex}`;
}

/**
 * Verify a webhook signature
 * Compares the received signature with the expected signature
 */
export async function verifyWebhookSignature(
  payload: string,
  signature: string,
  secret: string
): Promise<boolean> {
  const expectedSignature = await generateWebhookSignature(payload, secret);

  // Constant-time comparison to prevent timing attacks
  if (signature.length !== expectedSignature.length) {
    return false;
  }

  let result = 0;
  for (let i = 0; i < signature.length; i++) {
    result |= signature.charCodeAt(i) ^ expectedSignature.charCodeAt(i);
  }

  return result === 0;
}

/**
 * Generate a random webhook secret
 */
export function generateWebhookSecret(length: number = 32): string {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  const array = new Uint8Array(length);
  crypto.getRandomValues(array);

  return Array.from(array)
    .map(x => chars[x % chars.length])
    .join('');
}

/**
 * Parse webhook payload
 */
export function parseWebhookPayload<T = Record<string, unknown>>(
  payload: string
): T | null {
  try {
    return JSON.parse(payload) as T;
  } catch {
    return null;
  }
}

/**
 * Create webhook headers
 */
export function createWebhookHeaders(
  event: string,
  signature: string,
  timestamp: number = Date.now()
): Record<string, string> {
  return {
    'Content-Type': 'application/json',
    'X-Webhook-Event': event,
    'X-Webhook-Signature': signature,
    'X-Webhook-Timestamp': String(timestamp),
  };
}

export default {
  generateWebhookSignature,
  verifyWebhookSignature,
  generateWebhookSecret,
  parseWebhookPayload,
  createWebhookHeaders,
};

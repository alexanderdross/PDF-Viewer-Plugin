/**
 * Next.js middleware helpers for premium features
 */

import { NextRequest, NextResponse } from 'next/server';

/**
 * Password protection middleware options
 */
export interface PasswordMiddlewareOptions {
  /** Protected paths pattern */
  protectedPaths?: RegExp;
  /** Login redirect URL */
  loginUrl?: string;
  /** Cookie name for session */
  cookieName?: string;
  /** Skip authentication check function */
  skipAuth?: (request: NextRequest) => boolean;
}

/**
 * Create password protection middleware
 *
 * @example
 * ```tsx
 * // middleware.ts
 * import { createPasswordMiddleware } from '@pdf-embed-seo/react-premium/nextjs';
 *
 * const passwordMiddleware = createPasswordMiddleware({
 *   protectedPaths: /^\/pdf\/protected\/.+/,
 *   loginUrl: '/pdf/login',
 * });
 *
 * export function middleware(request: NextRequest) {
 *   return passwordMiddleware(request);
 * }
 * ```
 */
export function createPasswordMiddleware(options: PasswordMiddlewareOptions = {}) {
  const {
    protectedPaths = /^\/pdf\/protected\/.+/,
    loginUrl = '/pdf/login',
    cookieName = 'pdf_session',
    skipAuth,
  } = options;

  return function middleware(request: NextRequest): NextResponse | undefined {
    const { pathname } = request.nextUrl;

    // Check if path is protected
    if (!protectedPaths.test(pathname)) {
      return undefined;
    }

    // Allow skipping auth
    if (skipAuth?.(request)) {
      return undefined;
    }

    // Check for session cookie
    const session = request.cookies.get(cookieName);
    if (!session?.value) {
      // Redirect to login
      const url = new URL(loginUrl, request.url);
      url.searchParams.set('redirect', pathname);
      return NextResponse.redirect(url);
    }

    // Validate session (simple check - implement proper validation as needed)
    try {
      const sessionData = JSON.parse(atob(session.value));
      if (new Date(sessionData.expiresAt) < new Date()) {
        // Session expired
        const url = new URL(loginUrl, request.url);
        url.searchParams.set('redirect', pathname);
        const response = NextResponse.redirect(url);
        response.cookies.delete(cookieName);
        return response;
      }
    } catch {
      // Invalid session
      const url = new URL(loginUrl, request.url);
      url.searchParams.set('redirect', pathname);
      return NextResponse.redirect(url);
    }

    return undefined;
  };
}

/**
 * Role-based access middleware options
 */
export interface RoleMiddlewareOptions {
  /** Path to roles mapping */
  pathRoles: Record<string, string[]>;
  /** Get user roles function */
  getUserRoles: (request: NextRequest) => string[] | Promise<string[]>;
  /** Access denied redirect URL */
  accessDeniedUrl?: string;
}

/**
 * Create role-based access middleware
 *
 * @example
 * ```tsx
 * // middleware.ts
 * import { createRoleMiddleware } from '@pdf-embed-seo/react-premium/nextjs';
 *
 * const roleMiddleware = createRoleMiddleware({
 *   pathRoles: {
 *     '/pdf/admin/': ['admin'],
 *     '/pdf/members/': ['member', 'admin'],
 *   },
 *   getUserRoles: async (request) => {
 *     const session = await getSession(request);
 *     return session?.user?.roles || [];
 *   },
 * });
 *
 * export function middleware(request: NextRequest) {
 *   return roleMiddleware(request);
 * }
 * ```
 */
export function createRoleMiddleware(options: RoleMiddlewareOptions) {
  const { pathRoles, getUserRoles, accessDeniedUrl = '/403' } = options;

  return async function middleware(request: NextRequest): Promise<NextResponse | undefined> {
    const { pathname } = request.nextUrl;

    // Find matching path pattern
    const matchedPath = Object.keys(pathRoles).find((pattern) =>
      pathname.startsWith(pattern)
    );

    if (!matchedPath) {
      return undefined;
    }

    const requiredRoles = pathRoles[matchedPath];
    const userRoles = await getUserRoles(request);

    // Check if user has any required role
    const hasAccess = requiredRoles.some((role) => userRoles.includes(role));

    if (!hasAccess) {
      return NextResponse.redirect(new URL(accessDeniedUrl, request.url));
    }

    return undefined;
  };
}

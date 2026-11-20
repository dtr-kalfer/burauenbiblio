<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * CSRF (Cross-Site Request Forgery) protection helpers.
 *
 * Usage in a page that renders a form:
 *   <?php require_once("../shared/csrf.php"); ?>
 *   ...
 *   <input type="hidden" name="csrf_token" value="<?php echo Csrf::token(); ?>">
 *
 * Usage in a POST endpoint that performs a state change:
 *   require_once("../shared/csrf.php");
 *   Csrf::verify();   // dies with 403 on failure
 *
 * The token is stored in the session and is per-session, not per-form, so a
 * single token value is valid for every form the logged-in user submits until
 * the session is regenerated (e.g. on logout/login).
 */

class Csrf
{
    /**
     * Session key under which the token is stored.
     */
    const SESSION_KEY = 'csrf_token';

    /**
     * The name of the request field / header carrying the token.
     */
    const FIELD_NAME = 'csrf_token';

    /**
     * Return the current CSRF token, generating one if it does not yet exist.
     *
     * @return string
     */
    public static function token()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Verify the CSRF token from the current request.
     * Accepts the token from $_POST, $_GET, or the HTTP_X_CSRF_TOKEN header.
     * Emits a 403 and terminates on failure.
     *
     * @return void
     */
    public static function verify()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $expected = $_SESSION[self::SESSION_KEY] ?? '';
        $submitted = $_POST[self::FIELD_NAME]
            ?? $_GET[self::FIELD_NAME]
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? null;

        if ($expected === '' || $submitted === null) {
            self::fail();
        }

        // Timing-safe comparison to avoid leaking information via timing.
        if (!hash_equals($expected, (string) $submitted)) {
            self::fail();
        }
    }

    /**
     * Rotate the token (call after login to prevent session fixation).
     *
     * @return string The new token.
     */
    public static function rotate()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        return $_SESSION[self::SESSION_KEY];
    }

    private static function fail()
    {
        http_response_code(403);
        header('Content-Type: text/plain; charset=utf-8');
        echo '403 Forbidden: CSRF token validation failed.';
        exit;
    }
}
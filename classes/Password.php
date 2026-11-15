<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * Password hashing helper using PHP's password_hash() / password_verify().
 *
 * Provides backward-compatible verification of legacy MD5 hashes so that
 * existing users can log in seamlessly. On a successful legacy login the
 * hash is transparently upgraded to a modern bcrypt hash.
 *
 * PHP 5.5+ / 7.x / 8.x compatible.
 */

class Password
{

    public static function isLegacyHash($hash)
    {
        return (bool) preg_match('/^[a-f0-9]{32}$/i', (string) $hash);
    }


    public static function verify($password, $hash)
    {
        if ($hash === '' || $hash === null) {
            return false;
        }

        if (self::isLegacyHash($hash)) {
            // Legacy: compare MD5 hashes using a timing-safe comparison.
            return hash_equals($hash, md5($password));
        }

        // Modern hash — use PHP's built-in verification.
        return password_verify($password, $hash);
    }

    public static function needsRehash($hash)
    {
        if (self::isLegacyHash($hash)) {
            return true;
        }
        // password_needs_rehash is available in PHP 5.5+
        return password_needs_rehash($hash, PASSWORD_DEFAULT);
    }


    public static function hash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }


    public static function verifyAndUpgrade($password, $hash)
    {
        $valid = self::verify($password, $hash);
        if (!$valid) {
            return ['valid' => false, 'newHash' => null];
        }

        if (self::needsRehash($hash)) {
            return ['valid' => true, 'newHash' => self::hash($password)];
        }

        return ['valid' => true, 'newHash' => null];
    }
}
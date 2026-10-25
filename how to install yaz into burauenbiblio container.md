# Installing PHP YAZ in an OpenBiblio / BurauenBiblio Docker Container

This guide describes how to install and enable the PHP YAZ extension inside a Debian-based Docker container running OpenBiblio/BurauenBiblio with PHP 8.3 and PHP-FPM.

PHP YAZ enables PHP applications to use the YAZ toolkit for Z39.50 and related catalog-search workflows. Installing the `yaz` command-line utilities alone is **not enough**: BurauenBiblio needs the PHP extension loaded by its web-serving PHP runtime.

> **Important:** These commands assume Debian Bookworm, PHP 8.3 packages, and the Debian/Sury-style PHP configuration paths shown below. Check your container before using them. Package names and paths may differ for other distributions or PHP versions.

## 1. Enter the correct container

Run this command on the **Docker host**, not inside the container:

```bash
docker exec -it con_biblio bash
```

Replace `con_biblio` with your actual container name if different.

Confirm the environment:

```bash
cat /etc/os-release
php -v
php --ini
```

If `php` is not version 8.3, adjust the PHP package names and `/etc/php/8.3/` paths in this guide accordingly.

## 2. Refresh package indexes and check availability

Inside the container:

```bash
apt-get update

apt-cache policy php8.3-dev
apt-cache search yaz
apt-cache search php | grep -i yaz
```

A prebuilt PHP YAZ package may not be available in the configured repositories. This guide builds the extension with PECL instead.

## 3. Install build dependencies

Inside the container:

```bash
apt-get install -y \
  build-essential \
  php8.3-dev \
  php-pear \
  libyaz-dev \
  yaz
```

Review any proposed package changes before accepting them, particularly if APT proposes removing or changing the PHP runtime.

Verify the tools and versions:

```bash
php -v
phpize -v
php-config --version
yaz-config --version
pecl version
```

Confirm that `php-config --version` matches the PHP version you intend to use.

## 4. Build and install the PHP YAZ extension

Update the PECL channel and install YAZ:

```bash
pecl channel-update pecl.php.net
pecl install yaz-1.2.4
```

If the installer asks for the YAZ installation prefix, press **Enter** to accept the autodetected value unless your YAZ toolkit is installed in a nonstandard location.

When installation succeeds, PECL should report that `yaz-1.2.4` was installed. Find the extension:

```bash
find /usr/lib/php -name yaz.so -type f
```

The path may look like `/usr/lib/php/20230831/yaz.so` for PHP 8.3. **Use the path returned by your own command** rather than assuming that exact path.

## 5. Test the extension before enabling it globally

Copy the actual path from the previous command into the following examples. For example, if the path is `/usr/lib/php/20230831/yaz.so`:

```bash
php -n -d extension=/usr/lib/php/20230831/yaz.so --ri yaz
```

Then check the extension and main connection function:

```bash
php -n -d extension=/usr/lib/php/20230831/yaz.so -r '
var_dump(extension_loaded("yaz"));
var_dump(function_exists("yaz_connect"));
'
```

Expected output:

```text
bool(true)
bool(true)
```

The `-n` option skips the normal `php.ini` files, making this an isolated load test. If the test fails, inspect the error and check shared-library dependencies:

```bash
ldd /usr/lib/php/20230831/yaz.so
```

Use your actual `yaz.so` path in this command as well.

## 6. Enable YAZ for PHP CLI and PHP-FPM

Create the shared module configuration file:

```bash
printf '%s\n' 'extension=yaz.so' > /etc/php/8.3/mods-available/yaz.ini
```

Enable YAZ for CLI and PHP-FPM:

```bash
phpenmod -v 8.3 -s cli yaz
phpenmod -v 8.3 -s fpm yaz
```

Verify the CLI configuration:

```bash
php --ri yaz
php -r 'var_dump(extension_loaded("yaz"), function_exists("yaz_connect"));'
```

Expected values:

```text
bool(true)
bool(true)
```

Check that the FPM configuration includes YAZ:

```bash
ls -l /etc/php/8.3/fpm/conf.d/*yaz*
```

You should see a configuration link pointing to `mods-available/yaz.ini`.

## 7. Restart PHP-FPM

Inside the container:

```bash
service php8.3-fpm restart
```

If the service command is unavailable in your container, use the process supervisor or container startup method appropriate to that image. Avoid restarting the container unnecessarily while diagnosing a configuration issue.

> `docker restart ...` is a **Docker-host command**, not a command to run inside the container. Usually, restarting PHP-FPM is enough to load a changed PHP extension configuration. Restart the whole container only if needed, and run that command from the host.

## 8. Verify YAZ through the web application

Create a temporary file named `yaz_check.php` in the web root used by your BurauenBiblio site. For example, if `/var/www` is the actual document root:

```bash
cat > /var/www/yaz_check.php <<'PHP'
<?php
header('Content-Type: text/plain; charset=utf-8');

echo 'PHP Version: ', PHP_VERSION, PHP_EOL;
echo 'SAPI: ', PHP_SAPI, PHP_EOL;
echo 'YAZ loaded: ', extension_loaded('yaz') ? 'YES' : 'NO', PHP_EOL;
echo 'yaz_connect(): ', function_exists('yaz_connect') ? 'YES' : 'NO', PHP_EOL;
PHP
```

Open the file using the same domain and web route used to access BurauenBiblio. The expected output includes:

```text
PHP Version: 8.3.36
SAPI: fpm-fcgi
YAZ loaded: YES
yaz_connect(): YES
```

The exact PHP version may differ. The important checks are:

```text
SAPI: fpm-fcgi
YAZ loaded: YES
yaz_connect(): YES
```

If CLI reports YAZ enabled but the browser reports `NO`, check that you enabled the FPM configuration, restarted PHP-FPM, and placed the test file in the correct web root.

**Security:** Delete the temporary diagnostic file after testing, especially if the site is reachable from the internet:

```bash
rm -f /var/www/yaz_check.php
```

Adjust the path if you created the file elsewhere.

## 9. Disable YAZ temporarily for testing

You can disable YAZ without uninstalling the extension. This is useful for comparing BurauenBiblio behavior with and without the PHP YAZ functions.

### Disable YAZ for both CLI and FPM

Inside the container:

```bash
phpdismod -v 8.3 -s fpm yaz
phpdismod -v 8.3 -s cli yaz
service php8.3-fpm restart
```

Verify the CLI:

```bash
php -r 'var_dump(extension_loaded("yaz"), function_exists("yaz_connect"));'
```

Expected output:

```text
bool(false)
bool(false)
```

Reload the web-based `yaz_check.php`. It should report:

```text
YAZ loaded: NO
yaz_connect(): NO
```

### Disable YAZ only for the web application

If you want command-line PHP to retain YAZ for diagnostics while disabling it for BurauenBiblio's web requests, disable only FPM:

```bash
phpdismod -v 8.3 -s fpm yaz
service php8.3-fpm restart
```

### Re-enable YAZ

Enable the extension again for both SAPIs:

```bash
phpenmod -v 8.3 -s fpm yaz
phpenmod -v 8.3 -s cli yaz
service php8.3-fpm restart
```

The compiled `yaz.so` and the `mods-available/yaz.ini` file remain in place when the module is disabled using `phpdismod`.

## 10. Troubleshooting checklist

- **`Unable to locate package php8.3-dev`:** Refresh APT indexes and check the PHP package repository configured inside the container. Confirm the distribution and PHP version before selecting a package.
- **PECL cannot find YAZ:** Check that `libyaz-dev` is installed and that `yaz-config --version` works.
- **`php -n` cannot load `yaz.so`:** Confirm the exact extension path with `find /usr/lib/php -name yaz.so -type f`; then inspect dependencies with `ldd`.
- **CLI works but the browser does not:** Enable YAZ for FPM, restart PHP-FPM, and verify the web test file is served by the expected container and PHP runtime.
- **`yaz_connect()` is available but searches still fail:** The extension is loaded, but that does not prove the catalog connection, search syntax, host configuration, or MARC parsing is correct. Test those parts separately.
- **The extension disappears after container recreation:** Installing packages and PECL extensions in a running container changes that container's writable layer. Add the dependencies, PECL build, and PHP configuration to the Dockerfile or another reproducible image-build process.

## Quick verification commands

Run inside the container:

```bash
php --ri yaz
php -r 'var_dump(extension_loaded("yaz"), function_exists("yaz_connect"));'
```

For a browser-side check, use the temporary `yaz_check.php` file and remove it afterward.

**Success criteria:** PHP-FPM reports `YAZ loaded: YES` and `yaz_connect(): YES`. Then test BurauenBiblio's Z39.50 workflow independently.
"""

output_path = "/mnt/data/linux_php_yaz_openbiblio_burauenbiblio.md"
pypandoc.convert_text(markdown, "md", format="md", outputfile=output_path, extra_args=["--standalone"])
print(f"Created: {output_path}")

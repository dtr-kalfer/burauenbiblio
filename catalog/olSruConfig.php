<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 * Load SRU provider configurations.
 *
 * Returns an associative array of providers.
 */
function loadSruProviders(): array
{
		$config = require __DIR__ . '/config/sru_providers.php';

		if (json_last_error() !== JSON_ERROR_NONE) {
				throw new RuntimeException(
						'Invalid SRU provider configuration.'
				);
		}	
		return $config['providers'];
}


/**
 * Retrieve one provider configuration.
 */
function getSruProvider(string $providerId): ?array
{
    $providers = loadSruProviders();

    if (!isset($providers[$providerId])) {
        return null;
    }

    $provider = $providers[$providerId];

    if (empty($provider['enabled'])) {
        return null;
    }

    return $provider;
}

/**
 * Find an enabled SRU provider by its database host ID.
 */
function getSruProviderByHostId(int $hostId): ?array
{
    foreach (loadSruProviders() as $provider) {

        if (empty($provider['enabled'])) {
            continue;
        }

        if (
            isset($provider['host_id']) &&
            (int)$provider['host_id'] === $hostId
        ) {
            return $provider;
        }
    }

    return null;
}
<?php
declare(strict_types=1);

namespace InfoClient;

/**
 * class pour récupérer les information du client
 */
class InfoClient
{
    /**
     * donne l'adresse IP du client
     * @return string|null
     */
    public function getIp(): ?string
    {
        $keysRef = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        $keysServer = array_keys($_SERVER);
        /** @var ?string $keyFound */
        $keyFound = null;
        foreach ($keysServer as $key) {
            if (!is_null($keyFound) && in_array(strtoupper($key), $keysRef)) {
                $keyFound = $key;
            }
        }
        if (is_null($keyFound)) {
            return null;
        }
        foreach (explode(',', $_SERVER[$keyFound]) as $ip) {
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                return $ip;
            }
        }

        return null;
    }

    /**
     * donne le userAgent du client
     * @return string|null
     */
    public function getAgent(): ?string
    {
        if (!array_key_exists('HTTP_USER_AGENT', $_SERVER) || trim($_SERVER['HTTP_USER_AGENT']) === '') {
            return null;
        }
        return $_SERVER['HTTP_USER_AGENT'];
    }
}

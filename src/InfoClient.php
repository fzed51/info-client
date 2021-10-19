<?php
declare(strict_types=1);

namespace InfoClient;

/**
 * class pour récupérer les information du client
 */
class InfoClient
{

    /** @var array<string,string> donnee du serveur */
    private array $server;

    /**
     * constructeur
     * @param array<string,string>|null $stubServer pour les tests
     */
    public function __construct(?array $stubServer = null)
    {
        $this->server = array_change_key_case($stubServer ?? $_SERVER, CASE_UPPER);
    }

    /**
     * donne l'adresse IP du client
     * @return string|null
     */
    public function getIp(): ?string
    {
        $keysRef = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        $keysServer = array_keys($this->server);
        /** @var ?string $keyFound */
        $keyFound = null;
        foreach ($keysServer as $key) {
            if (is_null($keyFound) && in_array($key, $keysRef, true)) {
                $keyFound = $key;
            }
        }
        if (is_null($keyFound)) {
            return null;
        }
        foreach (explode(',', $this->server[$keyFound]) as $ip) {
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
        if (!array_key_exists('HTTP_USER_AGENT', $this->server) || trim($this->server['HTTP_USER_AGENT']) === '') {
            return null;
        }
        return $this->server['HTTP_USER_AGENT'];
    }
}

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
     * @param bool $allowAllRange
     * @return string|null
     */
    public function getIp(bool $allowAllRange = false): ?string
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
        $options = 0;
        if (!$allowAllRange) {
            $options |= FILTER_FLAG_NO_PRIV_RANGE;
            $options |= FILTER_FLAG_NO_RES_RANGE;
        }
        foreach (explode(',', $this->server[$keyFound]) as $ip) {
            $ip = trim($ip);

            if (filter_var($ip, FILTER_VALIDATE_IP, $options) !== false) {
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

    /**
     * cherche un des mot(s) dans un texte
     * @param array<string> $needles
     * @param string $haystak
     * @return bool
     */
    private static function searchOneOfString(array $needles, string $haystak): bool
    {
        $subPattern = implode('|', $needles);
        $pattern = "/$subPattern/i";
        return ($r = preg_match($pattern, $haystak)) !== false && $r > 0;
    }

    /**
     * extrait les info detaillees du user agent
     * @return array<string,string|null>
     */
    public function getInfos():array
    {
            $u_agent = $this->getAgent();
            $bname = 'Unknown';
            $ub = 'Unknown';
            $platform = 'Unknown';
            $version= "";
        if ($u_agent === null) {
            return [
                'userAgent' => null,
                'browser'      => $bname,
                'version'   => $version,
                'platform'  => $platform,
                'pattern'    => $bname
            ];
        }

            //First get the platform?
        if (self::searchOneOfString(['linux'], $u_agent)) {
            $platform = 'linux';
        } elseif (self::searchOneOfString(['macintosh','mac os x'], $u_agent)) {
            $platform = 'mac';
        } elseif (self::searchOneOfString(['windows','win32'], $u_agent)) {
            $platform = 'windows';
        }

            // Next get the name of the useragent yes seperately and for good reason
        if (self::searchOneOfString(['MSIE'], $u_agent) && !self::searchOneOfString(['Opera'], $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (self::searchOneOfString(['Firefox'], $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (self::searchOneOfString(['Edge'], $u_agent)) {
            $bname = 'Edge';
            $ub = "Edge";
        } elseif (self::searchOneOfString(['Chrome'], $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (self::searchOneOfString(['Safari'], $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (self::searchOneOfString(['Opera'], $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

            // finally get the correct version number
            $known = ['Version', $ub, 'other'];
            $pattern = '#(?<browser>' . implode('|', $known) .
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

            // see how many we have
            $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version= $matches['version'][0];
            } else {
                $version= $matches['version'][1];
            }
        } else {
            $version= $matches['version'][0];
        }

            // check if we have a number
        if ($version==null || $version=="") {
            $version="?";
        }

            return [
                'userAgent' => $u_agent,
                'browser'      => $bname,
                'version'   => $version,
                'platform'  => $platform,
                'pattern'    => $pattern
            ];
    }
}

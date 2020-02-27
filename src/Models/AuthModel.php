<?php


namespace Models;


use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;

class AuthModel
{
    /**
     * @param string $email
     * @param string $fistName
     * @param string $lastName
     * @param int $rank
     * @return string
     */
    public function generateJWT(string $email, string $fistName, string $lastName, int $rank): string
    {
        $signer = new Sha256();
        $private = new Key("file://../var/jwt/private.pem", "Maya666m");
        $time = time();
        $token = (new Builder())->issuedBy('http://api.moonly.fr')
            ->permittedFor('http://api.moonly.fr')
            ->identifiedBy('4f1g23a12aa', true)
            ->issuedAt($time)
            ->canOnlyBeUsedAfter($time)
            ->expiresAt($time + 3600)
            ->withClaim('email', $email)
            ->withClaim('firstName', $fistName)
            ->withClaim('lastName', $lastName)
            ->withClaim('rank', $rank)
            ->getToken($signer, $private);
        return $token;
    }

    /**
     * Used to convert token string to exploitable token (for check etc)
     * @param string $token
     * @return Token
     */
    public function parseToken(string $token): Token
    {
        return (new Parser())->parse($token);
    }

    /**
     * Check if token is valid by RSA key
     * @param string $token
     * @return bool
     */
    public function validateToken(string $token): bool
    {
        $signer = new Sha256();
        $jwt = $this->parseToken($token);
        $public = new Key('file://../var/jwt/public.pem');
        return $jwt->verify($signer, $public); //Check if signer if valid with our public key :bool
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isTokenExpired(string $token): bool
    {
        $jwt = $this->parseToken($token);
        $data = new ValidationData();
        $data->setCurrentTime(time());
        return $jwt->validate($data);
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isLogged(string $token): bool
    {
        if ($this->validateToken($token)) {
            return $this->isTokenExpired($token);
        } else {
            return false;
        }
    }

    /**
     * @param string $token
     * @return string
     */
    public function getTokenInfo(string $token): string
    {
        $realToken = $this->parseToken($token);

        return $realToken->getClaim('email');
    }

    public function getGrAvatarr(string $token, $s = 80, $d = 'mp', $r = 'g', $img = false, $atts = array()): string
    {
        $email = $this->getTokenInfo($token);
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val)
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }
}
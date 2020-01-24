<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class Users extends AbstractController
{


    /**
     * @Route("/api/user", name="api-main")
     */
    public function index()
    {
        $obj = new \stdClass();
        $obj->api = "ready";
        return new JsonResponse($obj);
    }


    /**
     * @Route("/api/register", name="register")
     */
    public function register(Request $request)
    {
        //todo ici on va faire le process de register
        /**
         * 1 - Collecte des forms
         * 2 - Verification Captcha
         * 3 - Verification email
         * 4 - Verification mot de passe
         * 5 - Creationdu token
         * 6 - Creation dans la base de donnée
         * 7 - Réponse OK + json
         * OU Réponse NOPE + Err.
         */
        $header = array('Access-Control-Allow-Origin *');
        if ($request->isMethod('POST')) {
            $obj = new \stdClass();
            $obj->data = "ok";

            $firstName = $request->get('firstName');
            $lastName = $request->get('lastName');
            $email = $request->get('email');
            $password = $request->get('password');
            $passwordConf = $request->get('passwordConf');
            $captcha = $this->captchaverify($request->get('captcha'));
            $obj->captcha = $captcha;
            /*$token = new \stdClass();
            $token->email = "mineswordcraft@gmail.com";
            $token->ip = "127.0.0.1";
            $token->exp = time() + 3600;
            $obj->token = base64_encode(json_encode($token));*/
            $signer = new Sha256();
            $time = time();

            $token = (new Builder())->issuedBy('http://example.com') // Configures the issuer (iss claim)
            ->permittedFor('http://example.org') // Configures the audience (aud claim)
            ->identifiedBy('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
            ->issuedAt($time) // Configures the time that the token was issue (iat claim)
            ->canOnlyBeUsedAfter($time + 60) // Configures the time that the token can be used (nbf claim)
            ->expiresAt($time + 3600) // Configures the expiration time of the token (exp claim)
            ->withClaim('uid', 1) // Configures a new claim, called "uid"
            ->getToken($signer, new Key('testing')); // Retrieves the generated token




            return new Response($token, Response::HTTP_OK);
        } else {
            return $this->redirectToRoute('api-main');
        }

    }


    function captchaverify($response): bool
    {
        $post = http_build_query(
            array(
                'response' => $response,
                'secret' => '6LfW180UAAAAAFgc6Iv7lzmpc2WEmA220wUs0iFP'
            )
        );
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'application/x-www-form-urlencoded',
                'content' => $post
            )
        );
        $context = stream_context_create($opts);
        $serverResponse = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        if (!$serverResponse) {
            return false;
        }
        $result = json_decode($serverResponse);
        if (!$result->success) {
            return false;
        }
        return (true);

    }

}
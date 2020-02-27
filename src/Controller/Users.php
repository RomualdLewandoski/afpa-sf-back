<?php


namespace App\Controller;


use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Models\AuthModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class Users extends AbstractController
{

    /**
     * @Route("/api/user", name="api-main")
     */
    public function index(): JsonResponse
    {
        $obj = new \stdClass();
        $obj->api = "ready";
        return new JsonResponse($obj);
    }


    /**
     * @Route("api/token", name="check_token")
     * @param Request $request
     * @return bool
     */
    public function checktoken(Request $request): JsonResponse
    {
        if ($request->isMethod('POST')) {
            $auth = new AuthModel();
            $obj = new \stdClass();
            $tokenStr = $request->get('token');
            if (empty($tokenStr)) {
                $obj->state = false;
            } else {
                if ($auth->validateToken($tokenStr)) {
                    $obj->state = $auth->isTokenExpired($tokenStr);
                    $obj->profile = $auth->getGrAvatarr($tokenStr);
                } else {
                    $obj->state = false;
                }
            }
            return new JsonResponse($obj);
        } else {

            $obj = new \stdClass();
            $obj->api = "ready";
            return new JsonResponse($obj);
        }
    }

    /**
     * @Route("/api/login", name="login")
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        if ($request->isMethod('POST')) {
            $obj = new \stdClass();
            $email = $request->get('email');
            $password = $request->get('password');
            if (empty($email) OR empty($password)) {
                $obj->state = 1;
                $obj->error = "Des champs sont manquants";
            } else {
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('App:Users')->findOneBy(array('email' => $email));
                if ($user != null) {
                    if ($this->verif_password($password, $user->getPassword())) {
                        $auth = new AuthModel();
                        $token = $auth->generateJWT($email, $user->getPrenom(), $user->getNom(), $user->getRank());
                        $user->setToken($token);
                        $em->flush();
                        $obj->state = 0;
                        $obj->token = $token;
                        $obj->redirect = "/#/app";
                    } else {
                        $obj->state = 1;
                        $obj->error = " Identifiant ou mot de passe incorrect";
                    }
                } else {
                    $obj->state = 1;
                    $obj->error = " Identifiant ou mot de passe incorrect";
                }
            }
            return new JsonResponse($obj);
        } else {
            $obj = new \stdClass();
            $obj->api = "ready";
            return new JsonResponse($obj);
        }
    }

    /**
     * @Route("/api/register", name="register")
     */
    public function register(Request $request): JsonResponse
    {
        if ($request->isMethod('POST')) {
            $obj = new \stdClass();

            $firstName = $request->get('firstName');
            $lastName = $request->get('lastName');
            $email = $request->get('email');
            $password = $request->get('password');
            $passwordConf = $request->get('passwordConf');
            $captcha = $this->captchaverify($request->get('captcha'));

            if (empty($firstName) OR empty($lastName)
                OR empty($email) OR empty($password)
                OR empty($passwordConf) OR empty($captcha)) {
                $obj->state = 1;
                $obj->error = "Des champs sont manquants";
            } else {
                if ($captcha) {
                    $em = $this->getDoctrine()->getManager();
                    $user = $em->getRepository('App:Users')->findBy(array('email' => $email));
                    if ($user == null) {
                        if ($password == $passwordConf) {
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $crypted_pass = $this->crypt_password($password);
                                $auth = new AuthModel();
                                $token = $auth->generateJWT($email, $firstName, $lastName, 0);
                                $user = new \App\Entity\Users();
                                $user->setPrenom($firstName);
                                $user->setNom($lastName);
                                $user->setEmail($email);
                                $user->setPassword($crypted_pass);
                                $user->setRank(0);
                                $user->setToken($token);
                                $user->setPremium(false);

                                $em->persist($user);
                                $em->flush();

                                $obj->state = 0;
                                $obj->token = $token;
                                $obj->redirect = "/#/app";
                            } else {
                                $obj->state = 1;
                                $obj->error = "Votre adresse email ne semble pas valide";
                            }
                        } else {
                            $obj->state = 1;
                            $obj->error = "Le mot de passe et sa confirmation n'est pas identique";
                        }

                    } else {
                        $obj->state = 1;
                        $obj->error = "L'adresse mail est déjà utilisée";
                    }
                } else {
                    $obj->state = 1;
                    $obj->error = "Verification du captcha incorrecte";
                }
            }
            return new JsonResponse($obj, Response::HTTP_OK);
        } else {
            $obj = new \stdClass();
            $obj->api = "ready";
            return new JsonResponse($obj);
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

    /**
     * @param $pass
     * @param $crypt
     * @return true if $pass and $crypt are the same, return false if not
     */
    function verif_password($pass, $crypt): bool
    {
        return password_verify($pass, $crypt);
    }

    /**
     * @param $pass
     * @return String with password encoding
     */
    function crypt_password($pass): string
    {
        return password_hash($pass, PASSWORD_DEFAULT);
    }


}
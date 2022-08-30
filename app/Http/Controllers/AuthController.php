<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use LdapRecord\Auth\BindException;
use LdapRecord\Connection;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'login_ad']]);
    }

    public function login_ad(Request $request)
    {
        $connection = new Connection([
            'hosts' => ['10.25.0.1'],
            'base_dn' => 'dc=tote, dc=local',
            'username' => 'ldap',
            'password' => 'iAcWMMqC@',

            // Optional Configuration Options
            'port' => 389,
            'use_ssl' => false,
            'use_tls' => false,
            'version' => 3,
            'timeout' => 5,
            'follow_referrals' => false,

        ]);

        $message = '';

        try {
            $connection->connect();

            $username = $request->input('email') . '@tote.local';
            $password = $request->input('password');

            if ($connection->auth()->attempt($username, $password)) {
                // Separa o nome e o sobrenome
                $separeName = explode(".", explode("@", $username)[0]);

                if (empty($separeName[1])) {
                    $separeName[1] = "";
                    $username = $separeName[0] . "@agetelecom.com.br";
                } else {
                    $username = $separeName[0] . "." . $separeName[1] . "@agetelecom.com.br";
                }

                $user = User::where('email', $username)->first();

                if(isset($user->email)) {

                    $password = '0@hnRB6R00qyRH&LHFg$zgWh3MHmOVo&$IliWjCr';

                    $auth = new AuthController();
                    return $auth->login($username, $password);


                } else {

                    $user = new User();
                    $password = '0@hnRB6R00qyRH&LHFg$zgWh3MHmOVo&$IliWjCr';

                    $user->create([
                        'name' => $separeName[0],
                        'email' => $username,
                        'password' => Hash::make($password),
                        'isMaster' => 0,
                        'isAdmin' => 0,
                        'isCommittee' => 0,
                    ]);

                    $auth = new AuthController();
                    return $auth->login($username, $password);

                }

            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

        } catch (BindException $e) {
//            $error = $e->getDetailedError();
//            echo $error->getErrorCode();
//            echo $error->getErrorMessage();
//            echo $error->getDiagnosticMessage();

            $auth = new AuthController();
            return $auth->login($request->input('email'), $request->input('password'));

        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login($email, $password)
    {
        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'name' => ucfirst(auth()->user()->name)
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use App\Models\BankAccount;

class JwtAuthController extends Controller
{

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * @OA\Post(
     * path="/api/auth/signin",
     * summary="Sign in",
     * description="Login by email, password",
     * operationId="login",
     * tags={"auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthorized",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="You are not authorized. Please try again")
     *        )
     *     )
     * )
     */
    /**
     * Get a JWT via given credentials.
    */
    public function login(Request $request){
    	$req = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:5',
        ]);

        if ($req->fails()) {
            return response()->json($req->errors(), 422);
        }

        if (! $token = auth()->attempt($req->validated())) {
            return response()->json(['Auth error' => 'Unauthorized'], 401);
        }

        return $this->generateToken($token);
    }

    /**
     * @OA\Post(
     * path="/api/auth/register",
     * summary="Sign up",
     * description="Register in ZZZ Bank Control System with your credentials",
     * operationId="register",
     * tags={"auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"username","firstname","lastname","email","password","confirm_password"},
     *       @OA\Property(property="username", type="string", example="suka.hujuka"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="firstname", type="string", example="Dobryna"),
     *       @OA\Property(property="lastname", type="string", example="Nikitich"),
     *       @OA\Property(property="password", type="string", format="password", example="FuckZmejHorynich"),
     *       @OA\Property(property="confirm_password", type="string", format="password", example="FuckZmejHorynich"),
     *    ),
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Validation error",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, invalid parameters. Please try again")
     *        )
     *     ),
     * )
     */
    /**
     * Sign up.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $req = Validator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
            'last_name'  => 'required|string|between:2,100',
            'username'   => 'required|string|between:2,100',
            'email'      => 'required|string|email|max:100|unique:users',
            'password'   => 'required|string|confirmed|min:6',
        ]);

        if($req->fails()){
            return response()->json($req->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
                    $req->validated(),
                    ['password' => bcrypt($request->password)]
                ));

        $bank_account = new BankAccount;
        $bank_account->user_id = $user->id;
        $bank_account->amount = 0;
        $bank_account->tax_percent = 0.5;
        $bank_account->save();

        return response()->json([
            'message'      => 'User signed up',
            'user'         => $user,
            'bank_account' => $bank_account,
        ], 200);
    }

    
    /**
     * @OA\Post(
     * path="/api/auth/signout",
     * summary="Log out",
     * description="Log out from ZZZ Bank Control System",
     * operationId="logout",
     * tags={"auth"},
     * @OA\Header(
     *     header="Authorization: Bearer <token>",
     *     description="Client's access token",
     *     @OA\Schema( type="string" )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="User logged out",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User logged out")
     *        )
     *     ),
     * )
     */
    /**
     * Sign out
    */
    public function signout() {
        auth()->logout();
        return response()->json(['message' => 'User loged out']);
    }

    /**
     * @OA\Post(
     * path="/api/auth/token-refresh",
     * summary="Refresh access token",
     * description="Refresh client's access token so he will not need a sign in again",
     * operationId="tokenrefresh",
     * tags={"auth"},
     * @OA\Header(
     *     header="Authorization: Bearer <token>",
     *     description="Client's access token",
     *     @OA\Schema( type="string" )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="token refreshed",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User logged out")
     *        )
     *     ),
     * )
     */
    /**
     * Token refresh
    */
    public function refresh() {
        return $this->generateToken(auth()->refresh());
    }

    /**
     * @OA\Get(
     * path="/api/auth/user",
     * summary="Get user info",
     * description="Get user info from ZZZ Bank Control System",
     * operationId="user",
     * tags={"auth"},
     * @OA\Header(
     *     header="Authorization: Bearer <token>",
     *     description="Client's access token",
     *     @OA\Schema( type="string" )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="User logged out",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User logged out")
     *        )
     *     ),
     * )
     */
    /**
     * User
    */
    public function user() {
        return response()->json(auth()->user());
    }

    /**
     * Generate token
    */
    protected function generateToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
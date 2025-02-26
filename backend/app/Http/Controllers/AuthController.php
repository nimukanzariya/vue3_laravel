<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Token;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends AccessTokenController
{
    public function login(ServerRequestInterface $request)
    {
        // Validation Rules.
        $rules = [
            'username' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ];

        // Validaton Custom Messages.
        $messages = [];

        // Input Validation.
        $validator = Validator::make($request->getParsedBody(), $rules, $messages);

        // On Validation Fail
        if ($validator->fails()) {
            // Converting Validation Errors Array Into Key Value Pair.
            foreach ($validator->messages()->getMessages() as $key => $value) {
                $errors[$key] = $value[0];
            }

            // Returning Response.
            return Controller::response('error', $errors, "Validation Failed");
        }

        try {
            DB::beginTransaction();
            $response = parent::issueToken($request);
            $data = json_decode($response->getContent(), true);

            if (isset($data['access_token'])) {
                $user = User::where('email', $request->getParsedBody()['username'])->first();

                // Customize the response by adding user details
                $data['user'] = $user;
            }
            DB::commit();
            return response()->json($data);
        } catch (OAuthServerException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            // Revoke all tokens for this user
            Token::where('user_id', $user->id)->update(['revoked' => true]);

            return response()->json(['message' => 'Successfully logged out.'], 200);
        }

        return response()->json(['message' => 'User not authenticated'], 401);
    }
}

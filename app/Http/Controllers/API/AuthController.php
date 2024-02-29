<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Auth;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
 * User Register
 * @param Request $request
 * @return response
 */
public function register(Request $request)
{        
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => [
            'required',
            'email',
            Rule::unique('users') // Ensure email is unique in the 'users' table
        ],
        'password' => 'required',
        'c_password' => 'required|same:password',
    ]);

    if ($validator->fails()) {
        $response = [
            'success' => false,
            'message' => $validator->errors()
        ];
        return response()->json($response, 400);
    }

    $input = $request->all();
    $input['password'] = bcrypt($input['password']);
    $user = User::create($input);

    $success['token'] = $user->createToken('MyApp')->plainTextToken;
    $success['name'] = $user->name; 
    $response = [
        'success' => true,
        'data' => $success,
        'message' => 'User Created Successfully'
    ];

    return response()->json($response, 200);
}

    /**
     * User Login
     * @param Request $request
     * @return response
     */
    public function login(Request $request)
    {   
        if(auth::attempt(['email'=>$request->email, 'password'=>$request->password]))   {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name']= $user->name; 

            $response = [
            'success'=> true,
            'data'=>$success,
            'message'=>' User Logged In Successfully'
            ];
            return response()->json($response, 200);
        }else {
            $response = [
                'success'=> false,
                'message'=>'Email or Password is incorrect'
            ];
            return response()->json($response);
        }
        
    }

    /**
 * User Logout
 * @param Request $request
 * @return response
 */
public function logout(Request $request)
{
    // Check if there is an authenticated user
    if (auth()->user()) {
        // Revoke all tokens associated with the user
        auth()->user()->tokens()->delete();
    }

    $response = [
        'success' => true,
        'message' => 'User logged out successfully'
    ];

    return response()->json($response, 200);
}
}

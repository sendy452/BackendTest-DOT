<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ],
        [
            'required'  => ':attribute harus diisi',
            'min'       => ':attribute minimal :min karakter',
        ]);

        if ($validator->fails()) {
            $resp = [
                'metadata' => [
                        'message' => $validator->errors()->first(),
                        'code'    => 422
                    ]
                ];
            return response()->json($resp, 422);
            die();
        }

        $user = User::where('email', $request->email)->first();
        if($user)
        {
                $token = \Auth::login($user);
                $resp = [
                    'response' => [
                        'token'=> $token,
                        'token_type' => 'bearer',  
                    ],
                    'metadata' => [
                        'message' => 'OK',
                        'code'    => 200
                    ]
                ];

                return response()->json($resp);
        }else{
            $resp = [
                'metadata' => [
                    'message' => 'Username Atau Password Tidak Sesuai',
                    'code'    => 401
                ]
            ];

            return response()->json($resp, 401);
        }

    }
}

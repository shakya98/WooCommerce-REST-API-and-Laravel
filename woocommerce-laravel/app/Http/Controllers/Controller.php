<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use App\Models\Products;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    //register function
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|max:255',
        ]);

        // dd($request->username);

        try {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'user' => $user,
                'token' => $user->createToken('token')->plainTextToken,
                'message' => 'Registered successfully'
            ], 201);
        } catch (\Exception $e) {
            // Return the exception message as error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // get woo products function
    public function getProducts()
    {
        $woo_products = Products::get(['id', 'name', 'price', 'description']);
        return $woo_products;
    }

}

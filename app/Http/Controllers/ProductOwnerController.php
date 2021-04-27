<?php

namespace App\Http\Controllers;

use App\Models\ProductOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductOwnerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): Object
    {
        $validator = Validator::make($request->all(), [
            'slack_hook_url' => 'required|url',
            'company_name' => 'required',
            'email' => 'email:rfc,dns|unique:product_owners',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $productOwner = new ProductOwner();
        $productOwner->id = (string) Str::uuid();
        $productOwner->slack_hook_url = $request->slack_hook_url;
        $productOwner->company_name = $request->company_name;
        $productOwner->email = $request->email;
        $productOwner->password =  Hash::make($request->password);
        
        $productOwner->save();

        return response()->json([
            'message' => 'ProductOwner Account Created Successfully'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductOwner  $productOwner
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): Object
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $productOwner = ProductOwner::where('email', $request->email)
            ->first();
        
        if(!$productOwner) 
            return response()->json([
                'message' => 'ProductOwner Account Not Found'
            ], 422);
        
        if(!Hash::check($request->password, $productOwner->password)) 
            return response()->json([
                'message' => 'ProductOwner Password Incorrect'
            ], 422);

        return response()->json([
            'message' => 'ProductOwner Login Successful', 
            'productOwner' => $productOwner
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductOwner  $productOwner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductOwner $productOwner): Object
    {
        $validator = Validator::make($request->all(), [
            'slack_hook_url' => 'required|url',
            'company_name' => 'required',
            'email' => 'email:rfc,dns|unique:product_owners,email,'.$productOwner->id,
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $productOwner->slack_hook_url = $request->slack_hook_url;
        $productOwner->company_name = $request->company_name;
        $productOwner->email = $request->email;
        
        $productOwner->save();

        return response()->json([
            'message' => 'ProductOwner Account Created Successfully', 
            'productOwner' => $productOwner
        ]);
    }
}

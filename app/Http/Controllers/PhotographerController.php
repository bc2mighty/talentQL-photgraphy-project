<?php

namespace App\Http\Controllers;

use App\Models\Photographer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\S3Upload;

class PhotographerController extends Controller
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
            'name' => 'required',
            'brand' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'email' => 'email:rfc,dns|unique:photographers',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }
      
        $photographer = new Photographer();
        $photographer->id = (string) Str::uuid();
        $photographer->name = $request->name;
        $photographer->brand = $request->brand;
        $photographer->phone = $request->phone;
        $photographer->address = $request->address;
        $photographer->email = $request->email;
        $photographer->password =  Hash::make($request->password);
        
        $photographer->save();

        return response()->json(['message' => 'Photographer Account Created Successfully']);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pictures(Request $request, Photographer $photographer, Product $product): Object
    {
        $validator = Validator::make($request->all(), [
            'pictures' => 'required|array',
            'pictures.*' => 'required|file|mimes:jpg,png,jpeg',
        ]);

        if($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        $pictures = $request->file('pictures');
        $s3Upload = new S3Upload();
        $response = $s3Upload->uploadAndGenerateThumbnail($pictures);
        
        dd(json_decode($response, true));

        return response()->json([
            'message' => 'Product Pictures',
            'pictures' => $pictures
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Photographer  $photographer
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

        $photographer = Photographer::where('email', $request->email)
            ->first();
        
        if(!$photographer) 
            return response()->json([
                'message' => 'Photographer Not Found'
            ], 422);
        
        if(!Hash::check($request->password, $photographer->password)) 
            return response()->json([
                'message' => 'Photographer Password Incorrect'
            ], 422);

        return response()->json([
            'message' => 'Photographer Login Successful', 
            'photographer' => $photographer
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Photographer  $photographer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Photographer $photographer): Object
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'brand' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'email' => 'email:rfc,dns|unique:photographers,email,'.$photographer->id,
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $photographer->name = $request->name;
        $photographer->brand = $request->brand;
        $photographer->phone = $request->phone;
        $photographer->address = $request->address;
        $photographer->email = $request->email;
        
        $photographer->save();

        return response()->json([
            'message' => 'Photographer Account Created Successfully', 
            'photographer' => $photographer
        ]);
    }
}

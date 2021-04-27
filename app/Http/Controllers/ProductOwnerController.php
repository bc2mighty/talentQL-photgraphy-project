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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slack_hook' => 'required',
            'slack_product_channel' => 'required',
            'brand' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'email' => 'email:rfc,dns|unique:product_owners',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        $product_owner = new ProductOwner();
        $product_owner->id = (string) Str::uuid();
        $product_owner->name = $request->name;
        $product_owner->brand = $request->brand;
        $product_owner->phone = $request->phone;
        $product_owner->address = $request->address;
        $product_owner->email = $request->email;
        $product_owner->password =  Hash::make($request->password);
        
        $product_owner->save();

        return response()->json(['message' => 'ProductOwner Account Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductOwner  $productOwner
     * @return \Illuminate\Http\Response
     */
    public function show(ProductOwner $productOwner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductOwner  $productOwner
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductOwner $productOwner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductOwner  $productOwner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductOwner $productOwner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductOwner  $productOwner
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductOwner $productOwner)
    {
        //
    }
}

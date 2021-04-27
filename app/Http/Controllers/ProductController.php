<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Get all products in processing facility
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function index(): Object
    {
        $products = Product::where('in_processing_facility', 1)
            ->get();

        return response()->json([
            'message' => 'All Products in processing facilities',
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): Object
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'product_owner_id' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $productOwner = ProductOwner::find($request->product_owner_id);
        
        if(!$productOwner) 
            return response()->json([
                'message' => 'Product Owner ID Not Found'
            ], 422);
      
        $product = new Product();
        $product->id = (string) Str::uuid();
        $product->title = $request->title;
        $product->product_owner_id = $request->product_owner_id;
        
        $product->save();

        return response()->json([
            'message' => 'Product Created Successfully',
            'product' => $product
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json([
            'message' => 'Product Details',
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'product_owner_id' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $productOwner = ProductOwner::find($request->product_owner_id);
        
        if(!$productOwner) 
            return response()->json([
                'message' => 'Product Owner ID Not Found'
            ], 422);
      
        $product->title = $request->title;
        $product->product_owner_id = $request->product_owner_id;
        
        $product->save();

        return response()->json([
            'message' => 'Product Updated Successfully',
            'product' => $product,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product): Object
    {
        $product->delete();
        // Delete Product Images

        return response()->json([
            'message' => 'Product Deleted Successfully'
        ]);
    }
}

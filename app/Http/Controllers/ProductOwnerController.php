<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOwner;
use App\Models\ProductPhotograph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\SlackNotification;
use Carbon\Carbon;

class ProductOwnerController extends Controller
{
    /**
     * Get all products created by Product Owner
     *
     * @param  \App\Models\ProductOwner  $productOwner
     * @return \Illuminate\Http\Response
     */
    public function products(ProductOwner $productOwner): Object
    {
        return response()->json([
            'message' => 'All Products Owned By product Owner',
            'products' => $productOwner->products,
        ]);
    }

    /**
     * Get All Unapproved Product Photograph Thumbnails.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductOwner  $productOwner
     * @return \Illuminate\Http\Response Object
     */

    public function unapproved(Request $request, ProductOwner $productOwner): Object
    {
        $photgraphs = $productOwner->unapprovedPhotographs;

        return response()->json([
            'message' => 'All unapproved product photographs', 
            'photgraphs' => $photgraphs, 
            'productOwner' => $productOwner
        ]);
    }

    /**
     * Get All Approved Product Photograph Thumbnails and high Resolution Images.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductOwner  $productOwner
     * @return \Illuminate\Http\Response Object
     */

    public function approved(Request $request, ProductOwner $productOwner): Object
    {
        $photgraphs = $productOwner->approvedPhotographs;

        return response()->json([
            'message' => 'All approved product photographs', 
            'photgraphs' => $photgraphs, 
            'productOwner' => $productOwner
        ]);
    }

    /**
     * Approve Product Photograph So that High resolution Images Can Show.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductOwner  $productOwner
     * @param  \App\Models\ProductPhotograph  $productPhotograph
     * @return \Illuminate\Http\Response Object
     */

    public function approve(Request $request, ProductOwner $productOwner, ProductPhotograph $productPhotograph): Object
    {
        $validator = Validator::make($request->all(), [
            'approved' => 'required|boolean',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error', 
                'errors' => $validator->errors()
            ], 422);
        }
        
        $productPhotograph->approved = $request->approved;
        $productPhotograph->save();
        
        if($request->approved) {
            $slackNotification = new SlackNotification($productPhotograph->product->product_owner->slack_hook_url);
            $response = $slackNotification->prepareAndSendMessage(
                "High Resolution Pictures for ".$productPhotograph->product->title,
                json_decode($productPhotograph->high_resolution_images, true),
                Carbon::now()->toFormattedDateString(),
                $productPhotograph->photographer->brand, 
                $productPhotograph->product->title
            );
        }

        $message = $request->approved ? 'Approved' : 'Disapproved';

        return response()->json([
            'message' => 'Product PhotoGraph '.$message.' Successfully', 
            'productPhotograph' =>  $request->approved ? $productOwner->approvedPhotographs : $productOwner->unapprovedPhotographs, 
            'productOwner' => $productOwner
        ]);
    }

    /**
     * Create Product Owner's Account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response Object
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
        ], 201);
    }

    /**
     * Set Products to be in Processing Facility so it could be
     * Accessible by Photographers for Capturin Photographs of such Product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductOwner  $productOwner
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response Object
     */

    public function product(Request $request, ProductOwner $productOwner, Product $product): Object
    {
        $validator = Validator::make($request->all(), [
            'in_processing_facility' => 'required|boolean',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error', 
                'errors' => $validator->errors()
            ], 422);
        }
        
        if($productOwner->id != $product->product_owner_id) 
            return response()->json([
                'message' => 'Product Owner and product mismatch'
            ], 422);
      
        $product->in_processing_facility = $request->in_processing_facility;
        
        $product->save();

        return response()->json([
            'message' => 'Product Updated Successfully',
            'product' => $product,
        ]);
    }

    /**
     * Login Product Owners.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response Object
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
     * Update Product Owner.
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

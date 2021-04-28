<?php

namespace App\Http\Controllers;

use App\Models\Photographer;
use App\Models\Product;
use App\Models\ProductOwner;
use App\Models\ProductPhotograph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\S3Upload;
use App\Services\SlackNotification;
use Carbon\Carbon;

class PhotographerController extends Controller
{
    /**
     * Store a newly created Photograph Object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response Object
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
            return response()->json([
                'message' => 'Validation Error', 
                'errors' => $validator->errors()
            ], 422);
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

        return response()->json([
            'message' => 'Photographer Account Created Successfully',
            'photographer' => $photographer
        ], 201);
    }

    /**
     * Store Product Pgotograph Objects.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Photographer  $photographer
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response Object
     */
    public function pictures(Request $request, Photographer $photographer, Product $product): Object
    {
        $validator = Validator::make($request->all(), [
            'pictures' => 'required|array',
            'pictures.*' => 'required|file|mimes:jpg,png,jpeg',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error', 
                'errors' => $validator->errors()
            ], 422);
        }

        // If Product Has Not been sent to processing facility
        // Unathorize Pictures upload for the product
        if(!$product->in_processing_facility) return response()->json([
                'message' => 'Product is not in processing facility'
            ], 422);


        // If Photographs have been taken for the Product
        // By the same Photographer that wants to post the pictures again
        // Unathorize such Photographer from performing that action
        $productPhotograph = ProductPhotograph::where([
            ['product_id', "=",$product->id],
            ['photographer_id', "=", $photographer->id],
        ])->count();
        
        if($productPhotograph > 0) return response()->json([
                'message' => 'Photographer Already Uploaded Pictures for this Product'
            ], 422);

        $pictures = $request->file('pictures');

        // S3Service Upload for storing both
        // High Resolution Images and It's Thumbnails
        $s3Upload = new S3Upload();
        $s3UploadResponse = json_decode($s3Upload->uploadAndGenerateThumbnail($pictures), true);
        
        // slackNotification Service Created for posting pictures uploaded
        // Notifications to the slack web hook url provided by the product owners
        $slackNotification = new SlackNotification($product->product_owner->slack_hook_url);
        $response = $slackNotification->prepareAndSendMessage(
            $s3UploadResponse['thumbnails'],
            Carbon::now()->toFormattedDateString(),
            $photographer->brand, 
            $product->title
        );
        
        // Save Product Photograph Links to s3 and Set Approval to false
        $productPhotograph = new ProductPhotograph();
        $productPhotograph->id = (string) Str::uuid();
        $productPhotograph->product_id = $product->id;
        $productPhotograph->photographer_id = $photographer->id;
        $productPhotograph->thumbnails = json_encode($s3UploadResponse['thumbnails']);
        $productPhotograph->high_resolution_images = json_encode($s3UploadResponse['highResolutions']);
        $productPhotograph->approved = false;

        $productPhotograph->save();
        $message = !$response ? 
            ' But Notification could not be sent because of incorrect Slack Web Hook. 
            You can view thumbnails json response instead from the API at 
            /api/productOwner/{productOwnerID}/products/photographs/unapproved' : '';

        return response()->json([
            'message' => 'Product Pictures Uploaded And Processed Successfully!'.$message,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
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

        // $token = $photographer->createToken('Auth')->accessToken;

        return response()->json([
            'message' => 'Photographer Login Successful', 
            'photographer' => $photographer,
            // 'token' => $token
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

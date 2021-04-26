<?php

namespace App\Http\Controllers;

use App\Models\Photographer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PhotographerController extends Controller
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
        $request->validate([
            'name' => 'required',
            'brand' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

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
     * Display the specified resource.
     *
     * @param  \App\Models\Photographer  $photographer
     * @return \Illuminate\Http\Response
     */
    public function show(Photographer $photographer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Photographer  $photographer
     * @return \Illuminate\Http\Response
     */
    public function edit(Photographer $photographer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Photographer  $photographer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Photographer $photographer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Photographer  $photographer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Photographer $photographer)
    {
        //
    }
}

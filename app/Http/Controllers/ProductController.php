<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
//use Psy\Util\Str;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private $product;
    /**
     * Display a listing of the resource.
     */

    public function __construct(Product $product){
        $this->product = $product;
    }
    public function index()
    {
        return Product::select('id','name','image','price')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
//        $all = Product::all();

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'required|image',
            'price' => 'required',

        ]
//            ,[
//                'name.required' => 'Ten khong duoc trong',
//                'price.required' => 'Gia ko dc truong',
//            ]
  );

//        $data = $request->product->create([
//            'name' => $request->name,
//            'image' => $request->image,
//            'price' => $request->price,
//        ]);
//
//        $data->save();

        try {
            $imageName = Str::random().'.'.$request->image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('product/image', $request->image, $imageName);
            Product::create($request->post()+['image'=>$imageName]);
            return response()->json([
                'message'=>'Product Created Successfully!!'
            ]);
        }catch (\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while creating a product!!'
            ],500);
        }

    }



    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}

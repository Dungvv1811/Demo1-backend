<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'name' => 'required|min:5|max:91',
            'image' => 'required|image',
            'price' => 'required|digits_between:4,22',

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

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|min:5|max:91',
            'image' => 'required|nullable',
            'price' => 'required|digits_between:4,22',
        ]);

        try {
            $product->fill($request->post())->update();
            if ($request->hasFile('image')) {
                if ($product->image) {
                    $exists = Storage::disk('public')->exists("product/image/{$product->image}");
                    if ($exists) {
                        Storage::disk('public')->delete("product/image/{$product->image}");

                    }
                }
                $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('product/image',$request->image,$imageName);
                $product->image = $imageName;
                $product->save();
            }
            return response()->json([
                'message' => 'Product Updated Successfully!!'
            ]);
        }catch (\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message' => 'Something goes wrong while updating a product!!'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            if ($product->image){
                $exists = Storage::disk('public')->exists("product/image/{$product->image}");
                if ($exists){
                    Storage::disk('public')->delete("product/image/{$product->image}");
                }
            }
            $product->delete();
            return response()->json([
                'message' => 'product Deleted Successfully!!'
            ]);
        } catch (\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while deleting a product!!'
            ]);
        }
    }
}

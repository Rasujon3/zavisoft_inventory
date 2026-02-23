<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use DataTables;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth_check');
    }
    public function index(Request $request)
    {
        try
        {
            if($request->ajax()){

                $data = Product::select('*')->latest();

                return Datatables::of($data)
                    ->addIndexColumn()

                    ->addColumn('name', function($row){
                        return $row->name ?? '';
                    })

                    ->addColumn('sku', function($row){
                        return $row->sku ?? '';
                    })

                    ->addColumn('purchase_price', function($row){
                        return $row->purchase_price ?? '';
                    })

                    ->addColumn('sell_price', function($row){
                        return $row->sell_price ?? '';
                    })

                    ->addColumn('opening_stock', function($row){
                        return $row->opening_stock ?? '';
                    })

                    ->addColumn('current_stock', function($row){
                        return $row->current_stock ?? '';
                    })

                    ->addColumn('action', function($row){

                        $btn = "";
                        $btn .= '&nbsp;';

                        $btn .= ' <a href="'.route('products.show',$row->id).'" class="btn btn-primary btn-sm action-button edit-product" data-id="'.$row->id.'"><i class="fa fa-edit"></i></a>';

                        $btn .= '&nbsp;';


                        $btn .= ' <a href="#" class="btn btn-danger btn-sm delete-data action-button" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a>';

                        return $btn;
                    })
                    // search customization
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && $request->search['value'] != '') {
                            $searchValue = $request->search['value'];
                            $query->where(function($q) use ($searchValue) {
                                $q->where('name', 'like', "%{$searchValue}%")
                                    ->orWhere('sku', 'like', "%{$searchValue}%")
                                    ->orWhere('purchase_price', 'like', "%{$searchValue}%")
                                    ->orWhere('opening_stock', 'like', "%{$searchValue}%");
                            });
                        }
                    })
                    ->rawColumns(['name', 'sku', 'purchase_price', 'sell_price', 'opening_stock', 'current_stock', 'action'])
                    ->make(true);
            }

            return view('admin.products.index');
        } catch(Exception $e) {
            // Log the error
            Log::error('Error in fetching data: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!!!'
            ],500);
        }
    }
    public function create()
    {
        return view('admin.products.create');
    }
    public function store(ProductRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $product = new Product();
            $product->name = $request->name;
            $product->sku = $request->sku;
            $product->purchase_price = $request->purchase_price;
            $product->sell_price = $request->sell_price;
            $product->opening_stock = $request->opening_stock;
            $product->current_stock = $request->opening_stock;
            $product->save();

            $notification=array(
                'message' => 'Successfully a data has been added',
                'alert-type' => 'success',
            );

            DB::commit();

            return redirect()->route('products.index')->with($notification);

        } catch(Exception $e) {
            DB::rollback();
            // Log the error
            Log::error('Error in storing data: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            $notification=array(
                'message' => 'Something went wrong!!!',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }
    public function show(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }
    public function edit(Product $product)
    {
        //
    }
    public function update(ProductRequest $request, Product $product)
    {
        try
        {
            $product->name = $request->name;
            $product->sku = $request->sku;
            $product->purchase_price = $request->purchase_price;
            $product->sell_price = $request->sell_price;
            $product->opening_stock = $request->opening_stock;
            $product->save();

            $notification=array(
                'message' => 'Successfully the data has been updated',
                'alert-type' => 'success',
            );

            return redirect()->route('products.index')->with($notification);

        } catch(Exception $e) {
            // Log the error
            Log::error('Error in updating data: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            $notification=array(
                'message' => 'Something went wrong!!!',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }
    public function destroy(Product $product)
    {
        try
        {
            $product->delete();
            return response()->json([
                'status'=>true,
                'message'=>'Successfully the data has been deleted'
            ]);
        } catch(Exception $e) {
            DB::rollback();
            // Log the error
            Log::error('Error in deleting data: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!!!'
            ]);
        }
    }
    public function branchStatusUpdate(Product $product)
    {
        DB::beginTransaction();
        try
        {
            $data = Product::findorfail($request->id);
            $data->status = $request->status;
            $data->update();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Product status updated successfully."
            ]);
        } catch(Exception $e) {
            DB::rollBack();
            // Log the error
            Log::error('Error in updating status: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => "Something went wrong!!!"
            ]);
        }
    }
}

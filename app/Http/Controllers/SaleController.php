<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleRequest;
use App\Models\Customer;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use DataTables;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
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

                $data = Sale::with('customer')->select('*')->latest();

                return Datatables::of($data)
                    ->addIndexColumn()

                    ->addColumn('invoice_no', function($row){
                        return $row->invoice_no ?? '';
                    })

                    ->addColumn('customer', function($row){
                        return $row->customer?->name ?? '';
                    })

                    ->addColumn('sale_date', function($row){
                        return $row->sale_date ?? '';
                    })

                    ->addColumn('sub_total', function($row){
                        return $row->sub_total ?? '';
                    })

                    ->addColumn('discount', function($row){
                        return $row->discount ?? '';
                    })

                    ->addColumn('vat_amount', function($row){
                        return $row->vat_amount ?? '';
                    })

                    ->addColumn('grand_total', function($row){
                        return $row->grand_total ?? '';
                    })

                    ->addColumn('paid_amount', function($row){
                        return $row->paid_amount ?? '';
                    })

                    ->addColumn('due_amount', function($row){
                        return $row->due_amount ?? '';
                    })

                    ->addColumn('action', function($row){

                        $btn = "";
                        $btn .= '&nbsp;';

                        #$btn .= ' <a href="'.route('sales.show',$row->id).'" class="btn btn-primary btn-sm action-button edit-product" data-id="'.$row->id.'"><i class="fa fa-edit"></i></a>';

                        $btn .= '&nbsp;';


                        $btn .= ' <a href="#" class="btn btn-danger btn-sm delete-data action-button" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a>';

                        return $btn;
                    })
                    // search customization
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && $request->search['value'] != '') {
                            $searchValue = $request->search['value'];
                            $query->where(function($q) use ($searchValue) {
                                $q->where('invoice_no', 'like', "%{$searchValue}%")
                                    ->orWhere('sale_date', 'like', "%{$searchValue}%")
                                    ->orWhere('paid_amount', 'like', "%{$searchValue}%")
                                    ->orWhere('due_amount', 'like', "%{$searchValue}%");
                            });
                        }
                    })
                    ->rawColumns(['invoice_no', 'customer', 'sale_date', 'sub_total', 'discount', 'vat_amount', 'grand_total', 'paid_amount', 'due_amount', 'action'])
                    ->make(true);
            }

            return view('admin.sales.index');
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
        $customers = Customer::get();
        $items = Product::get();

        return view('admin.sales.create', compact( 'customers', 'items'));
    }
    public function store(SaleRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $subTotal = 0;
            $cogsTotal = 0;

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->current_stock < $item['quantity']) {
                    DB::rollback();

                    $notification=array(
                        'message' => "Insufficient stock for {$product->name}",
                        'alert-type' => 'error'
                    );
                    return redirect()->back()->with($notification);
                }

                $subTotal += $item['quantity'] * $item['unit_price'];
                $cogsTotal += $item['quantity'] * $product->purchase_price;
            }

            $discount = $request->discount ?? 0;
            $vatPercent = $request->vat_percent ?? 0;
            $vatAmount = ($subTotal - $discount) * ($vatPercent / 100);
            $grandTotal = $subTotal - $discount + $vatAmount;
            $paidAmount = $request->paid_amount ?? 0;
            $dueAmount = $grandTotal - $paidAmount;

            $sale = new Sale();
            $sale->invoice_no = $request->invoice_no;
            $sale->customer_id = $request->customer_id;
            $sale->sale_date = $request->sale_date;
            $sale->sub_total = $subTotal;
            $sale->discount = $discount;
            $sale->vat_percent = $vatPercent;
            $sale->vat_amount = $vatAmount;
            $sale->grand_total = $grandTotal;
            $sale->paid_amount = $paidAmount;
            $sale->due_amount = $dueAmount;
            $sale->save();

            // Store Sale Items + Reduce Stock
            foreach ($request->items as $item) {

                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                $totalPrice = $item['quantity'] * $item['unit_price'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice,
                ]);

                // Reduce Stock
                $product->decrement('current_stock', $item['quantity']);
            }

            // 🔥 Create Journal Entry
            AccountingService::createSaleJournal($sale,$cogsTotal);

            DB::commit();

            $notification=array(
                'message' => 'Successfully a data has been added',
                'alert-type' => 'success',
            );

            return redirect()->route('sales.index')->with($notification);

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
    public function show(Sale $sale)
    {
        return view('admin.sales.edit', compact('sale'));
    }
    public function edit(Sale $sale)
    {
        //
    }
    public function update(SaleRequest $request, Sale $sale)
    {
        try
        {
            $sale->name = $request->name;
            $sale->phone = $request->phone;
            $sale->email = $request->email;
            $sale->address = $request->address;
            $sale->save();

            $notification=array(
                'message' => 'Successfully the data has been updated',
                'alert-type' => 'success',
            );

            return redirect()->route('sales.index')->with($notification);

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
    public function destroy(Sale $sale)
    {
        DB::beginTransaction();
        try
        {
            foreach ($sale->items as $item) {
                $item->product->increment('current_stock',$item->quantity);
            }

            JournalEntry::where('reference_type','sale')
                ->where('reference_id',$sale->id)
                ->delete();

            $sale->items()->delete();

            $sale->delete();

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
}

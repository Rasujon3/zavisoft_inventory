<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntryLine;
use App\Models\Sale;
use Illuminate\Http\Request;
use DataTables;

class ReportController extends Controller
{
    // Page
    public function salesReportPage()
    {
        $totalSum = Sale::sum('grand_total'); // Total sum for all sales
        return view('admin.reports.sales', compact('totalSum'));
    }

    // AJAX Data
    public function salesReportData(Request $request)
    {
        $query = Sale::query();

        if($request->from_date && $request->to_date){
            $query->whereBetween('sale_date', [$request->from_date, $request->to_date]);
        }

        if($request->status){
            $query->where('status', $request->status);
        }

        // ====== CLONE QUERY FOR TOTAL CALCULATION ======
        $totalSales = (clone $query)->sum('grand_total');
        $totalVAT   = (clone $query)->sum('vat_amount');

        // ====== COGS FROM JOURNAL ======
        $cogs = JournalEntryLine::whereHas('account', function($q){
            $q->where('type','expense');
        })
            ->when($request->from_date && $request->to_date, function($q) use ($request){
                $q->whereHas('journalEntry', function($qq) use ($request){
                    $qq->whereBetween('entry_date', [
                        $request->from_date,
                        $request->to_date
                    ]);
                });
            })
            ->sum('debit');

        $profit = $totalSales - $cogs;

        return DataTables::of($query)
            ->with([
                'totalSales' => number_format($totalSales,2),
                'totalVAT'   => number_format($totalVAT,2),
                'cogs'       => number_format($cogs,2),
                'profit'     => number_format($profit,2),
            ])
            ->addColumn('order_id', fn($row) => $row->id)
            ->addColumn('order_date', fn($row) => $row->sale_date)
            ->addColumn('customer_name', fn($row) => $row->customer_name)
            ->addColumn('customer_phone', fn($row) => $row->customer_phone)
            ->addColumn('total', fn($row) => number_format($row->grand_total,2))
            ->addColumn('status', fn($row) => $row->status)
            ->addColumn('courier_status', fn($row) => $row->courier_status ?? '-')
            ->make(true);
    }
    public function financial(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to ?? now()->endOfMonth()->toDateString();

        if ($from > $to) {
            return back()->with('error','Invalid date range');
        }

        $sales = Sale::whereBetween('sale_date', [$from, $to]);

        $totalSales = $sales->sum('grand_total');
        $totalVAT   = $sales->sum('vat_amount');

        $cogs = JournalEntryLine::whereHas('account', function($q){
            $q->where('type','expense');
        })
            ->whereHas('journalEntry', function($q) use ($from,$to){
                $q->whereBetween('entry_date',[$from,$to]);
            })
            ->sum('debit');

        return view('admin.reports.financial',compact(
            'from','to','totalSales','totalVAT','cogs'
        ));
    }
    public function profit(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to ?? now()->endOfMonth()->toDateString();

        $revenue = JournalEntryLine::whereHas('account', function($q){
            $q->where('type','income');
        })
            ->whereHas('journalEntry', function($q) use ($from,$to){
                $q->whereBetween('entry_date',[$from,$to]);
            })
            ->sum('credit');

        $expense = JournalEntryLine::whereHas('account', function($q){
            $q->where('type','expense');
        })
            ->whereHas('journalEntry', function($q) use ($from,$to){
                $q->whereBetween('entry_date',[$from,$to]);
            })
            ->sum('debit');

        $profit = $revenue - $expense;

        return view('admin.reports.profit',compact(
            'from','to','revenue','expense','profit'
        ));
    }
    public function ledger(Account $account, Request $request)
    {
        $from = $request->from ?? null;
        $to   = $request->to ?? null;

        $query = JournalEntryLine::where('account_id',$account->id)
            ->with('journalEntry')
            ->orderBy('id');

        if ($from && $to) {
            $query->whereHas('journalEntry', function($q) use ($from,$to){
                $q->whereBetween('entry_date',[$from,$to]);
            });
        }

        $lines = $query->get();

        $balance = 0;

        return view('admin.reports.ledger',compact(
            'account','lines','balance','from','to'
        ));
    }
}

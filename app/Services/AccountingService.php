<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use Exception;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    public static function createSaleJournal($sale, $cogsAmount)
    {
        DB::beginTransaction();

        try {

            $accounts = Account::pluck('id','name');

            $journal = JournalEntry::create([
                'entry_date' => $sale->sale_date,
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'description' => 'Sale Invoice '.$sale->invoice_no,
            ]);

            $lines = [
                // Debit AR
                ['account_id'=>$accounts['Accounts Receivable'],'debit'=>$sale->grand_total,'credit'=>0],

                // Credit Revenue
                ['account_id'=>$accounts['Sales Revenue'],'debit'=>0,'credit'=>$sale->sub_total - $sale->discount],

                // Credit VAT
                ['account_id'=>$accounts['VAT Payable'],'debit'=>0,'credit'=>$sale->vat_amount],

                // Debit COGS
                ['account_id'=>$accounts['Cost of Goods Sold'],'debit'=>$cogsAmount,'credit'=>0],

                // Credit Inventory
                ['account_id'=>$accounts['Inventory'],'debit'=>0,'credit'=>$cogsAmount],
            ];

            foreach($lines as $line){
                $journal->lines()->create($line);
            }

            self::validateBalanced($journal);

            DB::commit();

        } catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }

    private static function validateBalanced($journal)
    {
        $debit = $journal->lines()->sum('debit');
        $credit = $journal->lines()->sum('credit');

        if(round($debit,2) !== round($credit,2)){
            throw new Exception("Journal not balanced!");
        }
    }
}

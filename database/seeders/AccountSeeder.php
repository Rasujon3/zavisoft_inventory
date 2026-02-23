<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            ['name'=>'Cash','code'=>'1001','type'=>'asset'],
            ['name'=>'Accounts Receivable','code'=>'1002','type'=>'asset'],
            ['name'=>'Inventory','code'=>'1003','type'=>'asset'],
            ['name'=>'Sales Revenue','code'=>'4001','type'=>'income'],
            ['name'=>'VAT Payable','code'=>'2001','type'=>'liability'],
            ['name'=>'Cost of Goods Sold','code'=>'5001','type'=>'expense'],
        ];

        foreach($accounts as $acc){
            Account::firstOrCreate($acc);
        }
    }
}

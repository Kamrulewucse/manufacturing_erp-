<?php

namespace App\Imports;

use App\Enumeration\Type;
use App\Models\AccountHead;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\Cash;
use App\Models\CostCenter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CommonImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //Cash Account Head Code Import
//        foreach ($collection as $key => $data) {
//            if ($key != 0) {
//                $cash= new Cash();
//                $cash->cash_type_id = 1;
//                $cash->existing_account_code = $data[1];
//                $cash->general_ledger = $data[2];
//                $cash->sub_ledger = $data[3];
//                $cash->sub_subsidiary = $data[4];
//                $cash->account_code = $data[7];
//                $cash->name = $data[5];
//                $cash->save();
//            }
//        }

        //Bank Account Head Code Import
//        foreach ($collection as $key => $data) {
//
//               if ($data[1] == null){
//
//                   $bank = new Bank();
//                   $bank->name = $data[5];
//                   $bank->general_ledger = $data[2] ?? NULL;
//                   $bank->sub_ledger = $data[3];
//                   $bank->sub_subsidiary = $data[4];
//                   $bank->account_code = $data[7];
//                   $bank->save();
//
//                   $branch = new Branch();
//                   $branch->bank_id = $bank->id;
//                   $branch->name = 'Sylhet Gas Fields Limited';
//                   $branch->save();
//
//               }else{
//
//                   $bankLast = Bank::orderBy('id','desc')->first();
//                   $branchLast = Branch::orderBy('id','desc')->first();
//
//                   $bankAccount= new BankAccount();
//                   $bankAccount->bank_id = $bankLast->id;
//                   $bankAccount->branch_id = $branchLast->id;
//                   $bankAccount->account_name = $data[5];
//                   $bankAccount->account_no = $data[5];
//                   $bankAccount->existing_account_code = $data[1];
//                   $bankAccount->general_ledger = $data[2];
//                   $bankAccount->sub_ledger = $data[3];
//                   $bankAccount->sub_subsidiary = $data[4];
//                   $bankAccount->account_code = $data[7];
//                   $bankAccount->save();
//               }
//
//        }

        //Accounts Head code
//        foreach ($collection as $key => $data) {
//            if ($key != 0) {
//                $accountHead = new AccountHead();
//                $accountHead->existing_account_code = $data[1] ?? NULL;
//                $accountHead->general_ledger = $data[2] ?? NULL;
//                $accountHead->sub_ledger = $data[3] ?? NULL;
//                $accountHead->sub_subsidiary = $data[4] ?? NULL;
//
//                $accountCode = $data[2].$data[3].$data[4];
//
//                $accountHead->account_code = $accountCode ?? NULL;
//
//
//                $accountHead->name = $data[5] ?? NULL;
//
//                if ($data[6] == 'A')
//                    $accountHead->type = Type::$ASSET;
//                elseif($data[6] == 'L')
//                    $accountHead->type = Type::$LIABILITY;
//                elseif($data[6] == 'C')
//                    $accountHead->type = Type::$CAPITAL;
//                elseif($data[6] == 'E')
//                    $accountHead->type = Type::$EXPENSE;
//                elseif($data[6] == 'I')
//                    $accountHead->type = Type::$INCOME;
//                elseif($data[6] == 'R')
//                    $accountHead->type = Type::$RETAINED_EARNINGS;
//                else
//                    $accountHead->type = NULL;
//
//                $accountHead->save();
//
//            }
//        }
        //Cost Center
        foreach ($collection as $key => $data) {
            if ($key != 0) {
                $costCenter = new CostCenter();
                $costCenter->name = $data[1];
                $costCenter->save();
            }
        }
    }
}

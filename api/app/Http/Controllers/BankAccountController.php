<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\BankAccount;

class BankAccountController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function getBankAccount()
    {
        return BankAccount::where('user_id', auth()->user()->id)->first();
    }

    public function getAmount()
    {
        return BankAccount::where('user_id', auth()->user()->id)->first()->amount;
    }

    public function addAmount(Request $request)
    {
        $req = Validator::make($request->all(), [
            'amount'     => 'required|numeric',
        ]);

        if($req->fails()){
            return response()->json($req->errors()->toJson(), 400);
        }

        $bank_account = BankAccount::where('user_id', auth()->user()->id)->first();
        $bank_account->amount += $request->post('amount');
        $bank_account->save();
    }

    public function chargeAmount(Request $request)
    {
        $req = Validator::make($request->all(), [
            'amount'     => 'required|numeric',
        ]);

        if($req->fails()){
            return response()->json($req->errors()->toJson(), 400);
        }

        $bank_account = BankAccount::where('user_id', auth()->user()->id)->first();
        $amount = $request->post('amount');
        $tax = $amount * $bank_account->tax_percent / 100;
        $amount_with_tax = $amount + $tax;

        if ($bank_account->amount < $amount)
        {
            return response("ERROR: trying to charge ".$amount.", tax 0.5% is ".
                $tax.", whole amount is ".$amount_with_tax.
                ", it is more than user has on his bank account. Refused.", 406); // not acceptable
        }

        $bank_account->amount -= $amount_with_tax;
        $bank_account->save();

        return response(null, 200);
    }
}

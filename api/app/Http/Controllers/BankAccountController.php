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

    /**
     * @OA\Get(
     * path="/api/bank/details",
     * summary="Get user's bank account",
     * description="Get details about user's bank account",
     * operationId="details",
     * tags={"bank"},
     * @OA\Header(
     *     header="Authorization: Bearer <token>",
     *     description="Client's access token",
     *     @OA\Schema( type="string" )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="OK",
     *    @OA\JsonContent(
     *       @OA\Property(property="id", type="number", example="1"),
     *       @OA\Property(property="user_id", type="number", example="1"),
     *       @OA\Property(property="amount", type="decimal", example="345.34534"),
     *       @OA\Property(property="tax_percent", type="decimal", example="0.5"),
     *       @OA\Property(property="created_at", type="datetime", example="2021-01-27T11:03:49.000000Z"),
     *       @OA\Property(property="updated", type="datetime", example="2021-01-27T11:03:49.000000Z"),
     *        )
     *     ),
     * )
     */
    public function getBankAccount()
    {
        return BankAccount::where('user_id', auth()->user()->id)->first();
    }

    /**
     * @OA\Get(
     * path="/api/bank/amount",
     * summary="Get user's money on his bank account",
     * description="Get amount of money",
     * operationId="details",
     * tags={"bank"},
     * @OA\Header(
     *     header="Authorization: Bearer <token>",
     *     description="Client's access token",
     *     @OA\Schema( type="string" )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="OK",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="234.34344"),
     *        )
     *     ),
     * )
     */
    public function getAmount()
    {
        return BankAccount::where('user_id', auth()->user()->id)->first()->amount;
    }

    /**
     * @OA\Post(
     * path="/api/bank/add_amount",
     * summary="Add money on user's account",
     * description="Add money on user's account",
     * operationId="add_amount",
     * tags={"bank"},
     * @OA\Header(
     *     header="Authorization: Bearer <token>",
     *     description="Client's access token",
     *     @OA\Schema( type="string" )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="OK",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="OK"),
     *        )
     *     ),
     * )
     */
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


    /**
     * @OA\Post(
     * path="/api/bank/charge_amount",
     * summary="Charge money from user's bank account",
     * description="Charge money from user's bank account",
     * operationId="charge_amount",
     * tags={"bank"},
     * @OA\Header(
     *     header="Authorization: Bearer <token>",
     *     description="Client's access token",
     *     @OA\Schema( type="string" )
     * ),
     * @OA\Response(
     *    response=406,
     *    description="Not Acceptable",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="ERROR: trying to charge 45, tax 0.5% is 400000000, whole amount is 
     * , it is more than user has on his bank account. Refused."),
     *        )
     *     ),
     * )
     */
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

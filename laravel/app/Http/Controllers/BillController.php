<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Bill as BillResource;
use App\Http\Models\Bill;
use Illuminate\Http\Request;

class BillController extends Controller {
    public function index() {
		return BillResource::collection(Bill::all());
	}

	public function store(Request $request) {
		$bill_data = $request->validate([
			'PaymentAmount' => 'required',
			'PaymentDate' => 'required',
			'User' => 'required'
		]);
		$bill = Bill::create($bill_data);
		return (new BillResource($bill))->response()->setStatusCode(201);
	}

	public function update(Request $request, Bill $bill) {
		// TODO: check that user matches user id on bill
		$bill->update($request->all());
		return new BillResource($bill);
	}
	
	public function destroy(Bill $bill) {
		// TODO: check that user matches user id on bill
		$bill->delete();
		return response()->json(null, 204);
	}
}

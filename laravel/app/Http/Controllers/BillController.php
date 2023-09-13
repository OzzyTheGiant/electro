<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Bill as BillResource;
use App\Models\Bill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BillController extends Controller {
    public function __construct() {
        $this->middleware("auth:api");
        $this->middleware("web");
    }

    public function index(): AnonymousResourceCollection {
		return BillResource::collection(Bill::all());
	}

	public function store(Request $request): JsonResponse {
		$data = $request->validate([
			'user_id' => 'required',
			'payment_amount' => 'required',
			'payment_date' => 'required'
		]);

		$bill = Bill::create($data);
		return (new BillResource($bill))->response()->setStatusCode(201);
	}

	public function update(Request $request, Bill $bill): BillResource {
		$bill->update($request->all());
		return new BillResource($bill);
	}

	public function destroy(Bill $bill): JsonResponse {
		$bill->delete();
		return response()->json(null, 204);
	}
}

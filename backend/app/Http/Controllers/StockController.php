<?php

namespace App\Http\Controllers;

use App\Jobs\BulkStokUpload;
use App\Models\Stock;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    public function index(Request $request): mixed
    {
        // $query = Stock::query();

        // // Sorting
        // if ($request->has('sort')) {
        //     $sortField = $request->input('sort');
        //     $sortOrder = $request->input('order', 'asc');
        //     $query->orderBy($sortField, $sortOrder);
        // }

        // // Searching
        // if ($request->has('search') && !empty($request->input('search'))) {
        //     $search = $request->input('search');
        //     $query->where('name', 'LIKE', "%{$search}%");
        // }

        // // Pagination
        // $page = $request->input('page', 1);
        // $size = $request->input('size', 10);
        // $stocks = $query->paginate($size, ['*'], 'page', $page);
        // $extraData = [
        //     'last_page' => $stocks->lastPage(),
        //     'total' => $stocks->total(),
        // ];
        // return $this->response('success', $stocks->items(), 'Success!', $extraData);
        return $this->response('success', Stock::orderBy('id', 'DESC')->get(), 'Success!');
    }

    public function show($id): mixed
    {
        $stock = Stock::find($id);
        if (!$stock) {
            return $this->response('error', [], 'Data not Found!');
        }
        return $this->response('success', $stock, 'Success!');
    }

    public function store(Request $request): mixed
    {
        $data = $request->all();
        // Validation Rules.
        $rules = [
            'name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'store' => 'required|string',
        ];

        // Validaton Custom Messages.
        $messages = [];

        // Input Validation.
        $validator = Validator::make($data, $rules, $messages);

        // On Validation Fail
        if ($validator->fails()) {
            // Converting Validation Errors Array Into Key Value Pair.
            foreach ($validator->messages()->getMessages() as $key => $value) {
                $errors[$key] = $value[0];
            }

            // Returning Response.
            return $this->response('error', $errors, "Validation Failed");
        }
        try {
            DB::beginTransaction();

            Stock::create($data);

            DB::commit();
            return $this->response('success', [], 'Created data successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->response('error', [], $th->getMessage());
        }
    }

    public function update(Request $request, int $id): mixed
    {
        $data = $request->all();
        // Validation Rules.
        $rules = [
            'name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'store' => 'required|string',
        ];

        // Validaton Custom Messages.
        $messages = [];

        // Input Validation.
        $validator = Validator::make($data, $rules, $messages);

        // On Validation Fail
        if ($validator->fails()) {
            // Converting Validation Errors Array Into Key Value Pair.
            foreach ($validator->messages()->getMessages() as $key => $value) {
                $errors[$key] = $value[0];
            }

            // Returning Response.
            return $this->response('error', $errors, "Validation Failed");
        }
        try {
            DB::beginTransaction();

            $stock = Stock::find($id);
            if (!$stock) {
                return $this->response('error', [], 'Data not Found!');
            }

            $stock->update($data);

            DB::commit();

            return $this->response('success', [], 'Updated data successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->response('error', [], $th->getMessage());
        }
    }

    public function destroy(int $id): mixed
    {
        try {
            DB::beginTransaction();

            $stock = Stock::find($id);
            if (!$stock) {
                return $this->response('error', [], 'Data not Found!');
            }

            $stock->delete();

            DB::commit();

            return $this->response('success', [], 'Deleted Successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->response('error', [], $th->getMessage());
        }
    }

    public function bulkUpload(Request $request)
    {
        $data = $request->all();
        // Validation Rules.
        $rules = [
            'data' => 'required|array',
            'data.*.name' => 'required|string|max:255',
            'data.*.quantity' => 'required|numeric',
            'data.*.price' => 'nullable|numeric',
            'data.*.store' => 'nullable|string',
        ];

        // Validaton Custom Messages.
        $messages = [];

        // Input Validation.
        $validator = Validator::make($data, $rules, $messages);

        // On Validation Fail
        if ($validator->fails()) {
            // Converting Validation Errors Array Into Key Value Pair.
            foreach ($validator->messages()->getMessages() as $key => $value) {
                $errors[$key] = $value[0];
            }

            // Returning Response.
            return $this->response('error', $errors, "Validation Failed");
        }

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            DB::beginTransaction();
            // Dispatch job to queue
            BulkStokUpload::dispatch($data['data']);

            DB::commit();

            return $this->response('success', [], 'Created Successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->response('error', [], $th->getMessage());
        }
    }

    public function stores(): mixed
    {
        return $this->response('success', Store::select('name')->pluck('name'), 'Success!');
    }
}

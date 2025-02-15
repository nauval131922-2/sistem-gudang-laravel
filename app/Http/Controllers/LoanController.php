<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\UpdateLoanRequest;
use Illuminate\Support\Facades\Storage;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loans = Loan::select('id', 'item_id', 'borrower_name', 'loan_date', 'return_date', 'status')
            ->with(['item:id,name,image'])
            ->get();

        return response([
            'data' => $loans
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'borrower_name' => 'required',
            'loan_date' => 'required',
        ]);

        $loan = Loan::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $loan = Loan::find($id);
        return response([
            'data' => $loan
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loan $loan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'item_id' => 'required',
            'borrower_name' => 'required',
            'loan_date' => 'required',
        ]);

        $loan = Loan::find($id);
        
        $loan->update($request->all());

        // jika return_date diisi, ubah status menjadi returned
        if ($request->return_date) {
            $loan->status = 'returned';
        }
        return response([
            'data' => $loan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $loan = Loan::find($id);
        $loan->delete();

        return response([
            'data' => $loan
        ]);
    }
}

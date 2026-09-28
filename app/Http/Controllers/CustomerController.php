<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil data pelanggan sekaligus menghitung jumlah transaksinya
        $query = Customer::withCount('transactions')->orderBy('transactions_count', 'desc'); // Urutkan dari yang paling sering beli

        // Fitur Pencarian
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        $customers = Customer::withCount('transactions')->latest()->paginate(10);
        return view('customers.index', compact('customers'));
    }
}
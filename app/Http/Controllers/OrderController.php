<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::select('id', 'customer_name', 'table_no', 'order_date', 'order_time', 'status', 'total', 'waitress_id' , 'cashier_id')
            ->with(['waitress:id,name', 'cashier:id,name'])
            ->get();

        return response([
            'data' => $orders
        ]);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);

        return response([
            'data' => $order->loadMissing(['orderDetail:order_id,price,item_id,qty', 'orderDetail.item:id,name', 'waitress:id,name', 'cashier:id,name'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|max:100',
            'table_no' => 'required|max:5',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(['customer_name', 'table_no']);
            $data['order_date'] = date('Y-m-d');
            $data['order_time'] = date('H:i:s');
            $data['status'] = 'ordered';
            $data['total'] = 0;
            $data['waitress_id'] = auth()->user()->id;
            $data['items'] = $request->items;

            $order = Order::create($data);

            collect($data['items'])->map(function ($item) use ($order) {
                $foodDrink = Item::where('id', $item['id'])->first();
                OrderDetail::create([
                    'order_id' => $order->id,
                    'item_id' => $item['id'],
                    'price' => $foodDrink->price,
                    'qty' => $item['qty']
                ]);
            });

            $order->total = $order->sumOrderPrice();
            $order->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return response($th);
        }

        return response([
            'data' => $order
        ]);
    }

    public function setAsDone($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status != 'ordered') {
            return response('order already finished', 400);
        }

        $order->status = 'done';
        $order->save();

        return response([
            'data' => $order->loadMissing(['orderDetail:order_id,price,item_id,qty', 'orderDetail.item:id,name', 'waitress:id,name', 'cashier:id,name'])
        ]);
    }

    public function payment($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status != 'done') {
            return response('order not finished yet', 400);
        }

        $order->status = 'paid';
        $order->cashier_id = auth()->user()->id;
        $order->save();

        return response([
            'data' => $order->loadMissing(['orderDetail:order_id,price,item_id,qty', 'orderDetail.item:id,name', 'waitress:id,name', 'cashier:id,name'])
        ]);
    }

    public function orderReport(Request $request)
    {
        $orders = Order::whereMonth('order_date', $request->month)
            ->select('id', 'customer_name', 'table_no', 'order_date', 'order_time', 'status', 'total', 'waitress_id' , 'cashier_id')
            ->with(['waitress:id,name', 'cashier:id,name'])
            ->get();

        $orderCount = $orders->count();

        $maxPayment = $orders->max('total');
        $minPayment = $orders->min('total');

        $result = [
            'orders' => $orders,
            'orderCount' => $orderCount,
            'maxPayment' => $maxPayment,
            'minPayment' => $minPayment
        ];

        return response([
            'data' => $result
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\User;

class OrderController extends Controller
{
    //Para la vista de pedidos desde el dashboard
    public function globalIndex(){
        $orders = Order::query()
            ->with('user')
            ->orderBy('id')
            ->paginate(25);

        return view('admin.orders.index', compact('orders'));
    }

    public function globalShow(Order $order){
        $order->load([
            'user',
            'items.productVariant',
            'items.printJob',
            'shippingAddress.street.city.province.country',
            'billingAddress.street.city.province.country',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    //Para las vistas desde cada usuario
    public function userIndex(User $user)
    {
        $orders = Order::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(25);

        return view('admin.users.orders.index', compact('user', 'orders'));
    }

    public function userShow(User $user, Order $order){
        abort_unless($order->user_id === $user->id, 404);

        $order->load([
            'items.productVariant',
            'items.printJob',
            'shippingAddress.street.city.province.country',
            'billingAddress.street.city.province.country',
        ]);

        return view('admin.users.orders.show', compact('user', 'order'));
    }

    public function edit(Order $order)
    {
        // Cargamos items por si quieres mostrarlos (solo lectura) en el edit
        $order->load('items');

        return view('admin.orders.edit', [
            'order' => $order,
            'statuses' => OrderStatus::cases(),
            'paymentStatuses' => PaymentStatus::cases(),
        ]);
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->validated());

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Pedido actualizado correctamente.');
    }


}

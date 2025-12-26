<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Address;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Street;

class AddressController extends Controller
{
    public function index(User $user){
        $addresses = Address::query()
            ->where('user_id', $user->id)
            ->with(['street.city.province.country'])
            ->latest()
            ->get();

        return view('admin.users.addresses.index', compact('user', 'addresses'));
    }

    public function show(User $user, Address $address){
        abort_unless($address->user_id === $user->id, 404);

        $address->load(['street.city.province.country']);

        return view('admin.users.addresses.show', compact('user', 'address'));
    }

    public function edit(User $user, Address $address){
        abort_unless($address->user_id === $user->id, 404);

        $address->load(['street.city.province.country']);

        //Para evitar cargar demasiados datos, hago una búsqueda por texto simple en la vista para cargar una lista limitada
        $streets = Street::query()
            ->with(['city.province'])
            ->orderBy('name')
            ->limit(200) //Esto habrá qeu refinarlo
            ->get();

        return view('admin.users.addresses.edit', compact('user', 'address', 'streets'));
    }

    public function update(UpdateAddressRequest $request, User $user, Address $address){
        abort_unless($address->user_id === $user->id, 404);

        $address->update($request->validated());

        return redirect()
            ->route('admin.users.addresses.show', [$user, $address])
            ->with('status', 'Dirección actualizada correctamente');
    }

    public function destroy(User $user, Address $address){
        abort_unless($address->user_id === $user->id, 404);

        $address->delete();

        return redirect()
            ->route('admin.users.addresses.index', [$user])
            ->with('status', 'Dirección eliminada correctamente');
    }
}

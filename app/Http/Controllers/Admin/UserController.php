<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $users = User::query()
            ->orderBy('created_at')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['addresses.street']);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user){
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user){
        $user->update($request->validated());
        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', 'El usuario se ha actualizado correctamente');
    }

}

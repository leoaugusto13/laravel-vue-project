<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'Acesso não autorizado');
        }

        return User::orderBy('created_at', 'desc')->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'Acesso não autorizado');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'is_approved' => 'boolean',
            'role' => 'string|in:admin,user'
        ]);

        $user->update($validated);

        \App\Services\LoggerService::log('USER_UPDATE', "User profile updated");

        return response()->json([
            'message' => 'Usuário atualizado com sucesso!',
            'user' => $user
        ]);
    }

    /**
     * Approve or disapprove a user.
     */
    public function toggleApproval(Request $request, User $user)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'Acesso não autorizado');
        }

        $user->is_approved = !$user->is_approved;
        $user->save();

        $status = $user->is_approved ? 'approved' : 'disapproved';
        \App\Services\LoggerService::log('USER_APPROVAL_TOGGLE', "User status changed to {$status}");

        return response()->json([
            'message' => 'Status do usuário alterado com sucesso!',
            'user' => $user
        ]);
    }
}

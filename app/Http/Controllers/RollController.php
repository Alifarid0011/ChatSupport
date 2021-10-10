<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function ShowUsersWithOutRole()
    {
        $users = User::doesntHave('roles')->get();
        return response()->json([
            $users
        ], Response::HTTP_OK);
    }

    public function ShowUsersWithRole()
    {
        $users = User::has('roles')->with('roles')->get();
//        dd($users);
        return response()->json([
            $users
        ], Response::HTTP_OK);
    }

    public function CreateUsersRole(Request $request)
    {
        $users = User::whereId($request->data['user_id'])->first();
        $users->assignRole($request->data['role']);
        return response()->json([
            $users
        ], Response::HTTP_OK);
    }

    public function RemoveUsersRole(Request $request)
    {
        $userRole = User::find($request->toArray()['user_id']);
        $userRole->removeRole($request->toArray()['role']);
        return response()->json([
            true
        ], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $role = Role::create(['name' => $request->roll]);
        $role->givePermissionTo($request->permissions);
        return response()->json([
            'message' => 'Ok it is Created'
        ], Response::HTTP_OK);
    }

    public function createPermission(Request $request)
    {
//        dd($request->permission);
        Permission::create(['name' => $request->permission]);
        return response()->json([
            'message' => 'Ok it is Created'
        ], Response::HTTP_OK);
    }

    protected function show()
    {

        $rolls = Role::all();
        $rolls_permission = $rolls->each(function ($roll) {
            $roll->getAllPermissions();
        });
        $users = User::all();
        return View('dashboard', compact('rolls_permission', 'users'));
    }

    public function ShowRoll()
    {
        $rolls = Role::all();
        $rolls_permission = $rolls->each(function ($roll) {
            $roll->getAllPermissions();
        });
        return response()->json([
            $rolls_permission
        ], Response::HTTP_OK);
    }

    public function ShowPermission()
    {
        $permission = Permission::all();
        return response()->json([
            $permission
        ], Response::HTTP_OK);
    }

    public function RemoveRoll(Request $request)
    {
        $roll = Role::findByName($request->name);
        $roll->delete();
        return response()->json([
            true
        ], Response::HTTP_OK);
    }

    public function RemovePermission(Request $request)
    {
        $Permission = Permission::findByName($request->name);
        $Permission->delete();
        return response()->json([
            true
        ], Response::HTTP_OK);
    }
}

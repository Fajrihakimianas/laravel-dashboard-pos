<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //index
    public function index(Request $request)
    {
        //get all users with pagination
        $users = DB::table('users')->when($request->input('name'), function($query, $name) {
            $query->where('name', 'like', '%'. $name .'%')
                ->orWhere('email', 'like', '%'. $name .'%');
        })->paginate(10);
        return view('pages.users.index', compact('users'));
    }

    //create
    public function create()
    {
        return view('pages.users.create');
    }

    //store
    public function store(Request $request)
    {
        //validate the request
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'roles' => 'required|in:admin,staff,user',
        ]);

        //store the data
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->roles = $request->roles;
        $user->save();

        //redirect to the index page
        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    //show
    public function show($id)
    {
        return view('pages.auth.show');
    }

    //edit
    public function edit($id)
    {
        $user = User::findOrfail($id);
        return view('pages.users.edit', compact('user'));
    }

    //update
    public function update(Request $request, $id)
    {
        //validate the request
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'roles' => 'required|in:admin,staff,user',
        ]);

        //update the data
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->roles = $request->roles;
        $user->save();

        //if password is not empty
        if($request->password) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        //redirect to the index page
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    //destroy
    public function destroy($id)
    {
        //delete the data
        $user = User::find($id);
        $user->delete();

        //redirect to the index page
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}

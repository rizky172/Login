<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Libs\Repository\Finder\UserFinder;
use App\Libs\Repository\User;

use App\Models\User as Model;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $finder = new UserFinder();

        if(isset($request->deleted)){
            $finder->setOnlyTrashed($request->deleted);
        }

        $list = $finder->get();

        $data = [];
        foreach($list as $x) {
            $url = $x->media ? asset('storage/' . $x->username . '/' . $x->media) : null;

            $data[] = [
                'id' => $x->id,
                'username' => $x->username,
                'name' => $x->name,
                'email' => $x->email,
                'role' => $x->role,
                'url' => $url
            ];
        }

        return view('user', compact('data'));
    }

    public function formUser()
    {
        return view('add-user');
    }

    public function store(Request $request)
    {
        try {
            $row = Model::findOrNew($request->id);
            $row->name = $request->name;
            $row->username = $request->username;
            $row->email = $request->email;
            $row->role = $request->role;

            $repo = new User($row);
            
            if(!empty($request->password) && !empty($request->confirm_password)){
                $repo->addPassword($request->password, $request->confirm_password);
            }

            if ($request->hasFile('photo')) {
                $repo->addPhoto($request->file('photo'));
            }

            $repo->save();
            
            if(isset($request->type) && $request->type == 'profile'){
                return redirect()->route('profile', ['id' => $row->id])->with('success', 'Data berhasil disimpan.');
            }else{
                return redirect()->route('detail', ['id' => $row->id])->with('success', 'Data berhasil disimpan.');
            }
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    public function show($id)
    {
        $row = Model::withTrashed()->find($id);

        $repo = new User($row);
        $data = $repo->toArray();

        return view('edit-user', compact('data'));
    }

    public function profile($id)
    {
        $row = Model::withTrashed()->find($id);

        $repo = new User($row);
        $data = $repo->toArray();

        return view('profile', compact('data'));
    }

    public function destroy($id, $permanent=null)
    {
        try {
            $row = Model::withTrashed()->find($id);

            $repo = new User($row);
            $repo->delete($permanent);

            if($permanent){
                return redirect()->route('index.user', ['deleted' => 1])->with('success', 'Data berhasil dihapus.');
            }else{
                return redirect()->route('index.user')->with('success', 'Data berhasil dihapus.');
            }
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }        
    }

    public function restore($id)
    {
        $row = Model::withTrashed()->find($id);

        $repo = new User($row);
        $repo->restore();

        return redirect()->route('index.user', ['deleted' => 1])->with('success', 'Data berhasil dipulihkan.');
    }
}

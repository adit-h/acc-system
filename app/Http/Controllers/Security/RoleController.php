<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //code here
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $view = view('role-permission.form-role')->render();
        return response()->json(['data' =>  $view, 'status'=> true]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $req = $request->all();

        $role = new Role();
        $role->name = strtolower(str_replace(" ", "_", trim($req['title'])));
        $role->title = $req['title'];
        $role->status = intval($req['status']);
        $role->save();

        return redirect()->back()->withSuccess(__('message.msg_added',['name' => __('role.title')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       //code here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Role::findOrFail($id);
        //code here
        $view = view('role-permission.form-role', compact('data', 'id'))->render();
        return response()->json(['data' =>  $view, 'status'=> true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $req = $request->all();

        $role = Role::find($id);
        $role->name = strtolower(str_replace(" ", "_", trim($req['title'])));
        $role->title = $req['title'];
        $role->status = intval($req['status']);
        $role->save();

        return redirect()->back()->withSuccess(__('message.msg_updated',['name' => __('role.title')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $status = 'error';
        $message = __('global-message.delete_form', ['form' => __('role.title')]);

        if ($role != '') {
            $role->delete();
            $status = 'success';
            $message = __('global-message.delete_form', ['form' => __('role.title')]);
        }

        return redirect()->back()->withSuccess(__($message));
    }
}

<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
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
        $view = view('role-permission.form-permission')->render();
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

        $perm = new Permission();
        $perm->name = strtolower(str_replace(" ", "_", trim($req['title'])));
        $perm->title = $req['title'];
        $perm->guard_name = 'web';
        $perm->save();

        return redirect()->back()->withSuccess(__('message.msg_added',['name' => __('permission.title')]));
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
        $data = Permission::findOrFail($id);
        //code here
        $view = view('role-permission.form-permission', compact('data', 'id'))->render();
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

        $perm = Permission::find($id);
        $perm->name = strtolower(str_replace(" ", "-", trim($req['title'])));
        $perm->title = $req['title'];
        $perm->save();

        return redirect()->back()->withSuccess(__('message.msg_updated',['name' => __('permission.title')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $perm = Permission::findOrFail($id);
        $status = 'error';
        $message = __('global-message.delete_form', ['form' => __('permission.title')]);

        if ($perm != '') {
            $perm->delete();
            $status = 'success';
            $message = __('global-message.delete_form', ['form' => __('permission.title')]);
        }

        return redirect()->back()->withSuccess(__($message));
    }
}

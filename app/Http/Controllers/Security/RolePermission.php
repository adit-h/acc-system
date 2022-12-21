<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Models\RoleHasPermission;

class RolePermission extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::get();
        $permissions = Permission::get();
        $url = route('role.permission.ajax-update');

        return view('role-permission.permissions', compact('roles', 'permissions', 'url'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //code here
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $req = $request->all();
        dump($req);

        return redirect()->back()->withSuccess(__('message.msg_updated',['name' => __('permission.title')]));
    }

    public function ajaxUpdate(Request $request)
    {
        $arr = $request->data;
        // clear data
        RoleHasPermission::truncate();
        // prepare data
        $data = [];
        for ($i=0; $i<count($arr); $i++) {
            $data[] = [
                "permission_id" => $arr[$i][1],
                "role_id" => $arr[$i][0]
            ];
        }
        // re-insert array
        RoleHasPermission::insert($data);

        $res = [
            "data" => $data,
            "status" => 'success',
        ];

        return $res;
    }
}

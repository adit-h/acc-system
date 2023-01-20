<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransLimit;
use App\Helpers\AuthHelper;
use App\Http\Requests\TransLimitRequest;

class TransLimitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auth_user = AuthHelper::authSession();
        $data = TransLimit::first();

        $id = $data->id;
        $slist = [
            0 => 'Inactive',
            1 => 'Active'
        ];
        //dump($setting);

        return view('trans-limit.form', compact('data', 'id', 'slist'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $setting = TransLimit::get()->pluck('title', 'id');

        return view('trans-limit.form', compact('setting'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransLimitRequest $request)
    {
        $req = $request->all();
        $req['type'] = 'global';    // set default

        $setting = TransLimit::create($req);

        return redirect()->route('transLimit.index')->withSuccess(__('message.msg_added', ['name' => __('trans-limit.store')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = TransLimit::findOrFail($id);

        return view('trans-limit.form', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = TransLimit::findOrFail($id);

        return view('trans-limit.form', compact('data', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TransLimitRequest $request, $id)
    {
        $data = $request->all();
        $setting = TransLimit::findOrFail($id);

        $setting['date_start'] = date('Y-m-d', strtotime($data['date_start']));
        $setting['date_end'] = date('Y-m-d', strtotime($data['date_end']));
        $setting['status'] = $data['status'];
        $setting->save();

        return redirect()->route('transLimit.index')->withSuccess(__('message.msg_updated', ['name' => 'trans-limit.store']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}

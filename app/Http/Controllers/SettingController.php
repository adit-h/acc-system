<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\SettingDataTable;
use App\Models\Setting;
use App\Helpers\AuthHelper;
use App\Http\Requests\SettingRequest;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SettingDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('setting.title')]);
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('settings.create') . '" class="btn btn-sm btn-primary" role="button">Add Setting</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $setting = Setting::get()->pluck('title', 'id');

        return view('settings.form', compact('setting'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SettingRequest $request)
    {
        $req = $request->all();
        $req['type'] = 'global';    // set default

        $setting = Setting::create($req);

        return redirect()->route('settings.index')->withSuccess(__('message.msg_added', ['name' => __('settings.store')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Setting::findOrFail($id);

        return view('settings.profile', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Setting::findOrFail($id);

        return view('settings.form', compact('data', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SettingRequest $request, $id)
    {
        $setting = Setting::findOrFail($id);
        $setting->fill($request->all())->update();

        return redirect()->route('settings.index')->withSuccess(__('message.msg_updated', ['name' => 'settings.store']));
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

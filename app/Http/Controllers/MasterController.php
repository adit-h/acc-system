<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\MasterAccountDataTable;
use App\Models\MasterAccount;
use App\Models\AccountCategory;
use App\Helpers\AuthHelper;
use App\Http\Requests\MasterAccountRequest;

class MasterController extends Controller
{
    /**
     * Display a list of the Master Account.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(MasterAccountDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title',['form' => trans('masters.title')] );
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="'.route('master.create').'" class="btn btn-sm btn-primary" role="button">Add Account</a>';
        return $dataTable->render('global.datatable', compact('pageTitle','auth_user','assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = AccountCategory::get()->pluck('name', 'id');

        return view('masters.form', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MasterAccountRequest $request)
    {
        $account = MasterAccount::create($request->all());

        return redirect()->route('master.account')->withSuccess(__('message.msg_added',['name' => __('masters.store')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = MasterAccount::with('accountCategory')->findOrFail($id);
        // TODO : create view blade
        // return view('masters.view', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = MasterAccount::with('accountCategory')->findOrFail($id);

        $category = AccountCategory::get()->pluck('name', 'id');

        return view('masters.form', compact('data', 'id', 'category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MasterAccountRequest $request, $id)
    {
        // dd($request->all());
        $account = MasterAccount::with('accountCategory')->findOrFail($id);

        // Update master account data...
        $account->fill($request->all())->update();

        if(auth()->check()){
            return redirect()->route('master.account')->withSuccess(__('message.msg_updated',['name' => __('message.account')]));
        }
        return redirect()->back()->withSuccess(__('message.msg_updated',['name' => 'Master Account']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $account = MasterAccount::findOrFail($id);
        $status = 'errors';
        $message= __('global-message.delete_form', ['form' => __('masters.title')]);

        if ($account != '') {
            $account->delete();
            $status = 'success';
            $message= __('global-message.delete_form', ['form' => __('masters.title')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message, 'datatable_reload' => 'dataTable_wrapper']);
        }

        return redirect()->back()->with($status,$message);
    }
}

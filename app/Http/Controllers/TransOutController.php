<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\TransactionOutDataTable;
use App\Models\TransactionOut;
use App\Models\MasterAccount;
use App\Helpers\AuthHelper;
use App\Http\Requests\TransactionOutRequest;

class TransOutController extends Controller
{
    /**
     * Display a list of the Trans Out.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransactionOutDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title',['form' => trans('transactions-out.title')] );
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="'.route('trans.out.create').'" class="btn btn-sm btn-primary" role="button">Add Trans</a>';
        return $dataTable->render('global.datatable', compact('pageTitle','auth_user','assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $acc_from = MasterAccount::get()->pluck('name', 'id');
        $acc_to = MasterAccount::get()->pluck('name', 'id');

        return view('trans-out.form', compact('acc_from', 'acc_to'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionOutRequest $request)
    {
        $account = TransactionOut::create($request->all());

        return redirect()->route('trans.out.index')->withSuccess(__('message.msg_added',['name' => __('transactions-out.title')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = TransactionOut::with('receiveFrom')->with('storeTo')->findOrFail($id);
        // TODO : create view blade
        // return view('transactions-out.view', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = TransactionOut::with('receiveFrom')->with('storeTo')->findOrFail($id);

        $acc_from = MasterAccount::get()->pluck('name', 'id');
        $acc_to = MasterAccount::get()->pluck('name', 'id');

        return view('trans-out.form', compact('data', 'id', 'acc_from', 'acc_to'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TransactionOutRequest $request, $id)
    {
        //dd($request->all());
        $trans = TransactionOut::with('receiveFrom')->with('storeTo')->findOrFail($id);

        // Update master account data...
        $trans->fill($request->all())->update();

        if(auth()->check()){
            return redirect()->route('trans.out.index')->withSuccess(__('message.msg_updated',['name' => __('transactions-out.title')]));
        }
        return redirect()->back()->withSuccess(__('message.msg_updated',['name' => __('transactions-out.title')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $account = TransactionOut::findOrFail($id);
        $status = 'errors';
        $message= __('global-message.delete_form', ['form' => __('transactions-out.title')]);

        if ($account != '') {
            $account->delete();
            $status = 'success';
            $message= __('global-message.delete_form', ['form' => __('transactions-out.title')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message, 'datatable_reload' => 'dataTable_wrapper']);
        }

        return redirect()->back()->with($status, $message);
    }
}

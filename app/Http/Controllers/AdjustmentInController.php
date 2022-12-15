<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\AdjustmentInDataTable;
use App\Models\TransactionIn;
use App\Models\MasterAccount;
use App\Helpers\AuthHelper;
use App\Http\Requests\TransactionInRequest;

class AdjustmentInController extends Controller
{
    /**
     * Display a list of the Trans In.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AdjustmentInDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title',['form' => trans('Adjustment-in.title')] );
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="'.route('adjustment.in.create').'" class="btn btn-sm btn-primary" role="button">Add Adjustment</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $acc_from = MasterAccount::where('status', 'active')->pluck('name', 'id');
        $acc_to = MasterAccount::where('status', 'active')->pluck('name', 'id');

        return view('adjustment-in.form', compact('acc_from', 'acc_to'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionInRequest $request)
    {
        // dd($request->all());
        $trans = TransactionIn::create($request->all());

        return redirect()->route('adjustment.in.index')->withSuccess(__('message.msg_added',['name' => __('adjustment-in.title')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = TransactionIn::with('receiveFrom')->with('storeTo')->findOrFail($id);
        // TODO : create view blade
        // return view('adjustment-in.view', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = TransactionIn::with('receiveFrom')->with('storeTo')->findOrFail($id);

        $acc_from = MasterAccount::where('status', 'active')->pluck('name', 'id');
        $acc_to = MasterAccount::where('status', 'active')->pluck('name', 'id');

        return view('adjustment-in.form', compact('data', 'id', 'acc_from', 'acc_to'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TransactionInRequest $request, $id)
    {
        // dd($request->all());
        $trans = TransactionIn::with('receiveFrom')->with('storeTo')->findOrFail($id);

        // Update master account data...
        $trans->fill($request->all())->update();

        if(auth()->check()){
            return redirect()->route('adjustment.in.index')->withSuccess(__('message.msg_updated',['name' => __('adjustment-in.title')]));
        }
        return redirect()->back()->withSuccess(__('message.msg_updated',['name' => __('adjustment-in.title')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trans = TransactionIn::findOrFail($id);
        $status = 'errors';
        $message= __('global-message.delete_form', ['form' => __('adjustment-in.title')]);

        if ($trans != '') {
            $trans->delete();
            $status = 'success';
            $message= __('global-message.delete_form', ['form' => __('adjustment-in.title')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message, 'datatable_reload' => 'dataTable_wrapper']);
        }

        return redirect()->back()->with($status, $message);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\GoodsCostDataTable;
use App\Models\TransactionOut;
use App\Models\MasterAccount;
use App\Helpers\AuthHelper;
use App\Http\Requests\TransactionOutRequest;

class GoodsCostController extends Controller
{
    /**
     * Display a list of the Trans In.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GoodsCostDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title',['form' => trans('goods-cost.title')] );
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="'.route('goods.cost.create').'" class="btn btn-sm btn-primary" role="button">Add Cost</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $acc_from = MasterAccount::whereIn('code', ["7003"])->pluck('name', 'id');
        $acc_to = MasterAccount::whereIn('code', ["2000"])->pluck('name', 'id');

        return view('goods-cost.form', compact('acc_from', 'acc_to'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionOutRequest $request)
    {
        // dd($request->all());
        $trans = TransactionOut::create($request->all());

        return redirect()->route('goods.cost.index')->withSuccess(__('message.msg_added',['name' => __('goods-cost.title')]));
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
        // return view('goods-cost.view', compact('data'));
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

        $acc_from = MasterAccount::whereIn('code', ["7003"])->pluck('name', 'id');
        $acc_to = MasterAccount::whereIn('code', ["2000"])->pluck('name', 'id');

        return view('goods-cost.form', compact('data', 'id', 'acc_from', 'acc_to'));
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
        // dd($request->all());
        $trans = TransactionOut::with('receiveFrom')->with('storeTo')->findOrFail($id);

        // Update master account data...
        $trans->fill($request->all())->update();

        if(auth()->check()){
            return redirect()->route('goods.cost.index')->withSuccess(__('message.msg_updated',['name' => __('goods-cost.title')]));
        }
        return redirect()->back()->withSuccess(__('message.msg_updated',['name' => __('goods-cost.title')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trans = TransactionOut::findOrFail($id);
        $status = 'errors';
        $message= __('global-message.delete_form', ['form' => __('goods-cost.title')]);

        if ($trans != '') {
            $trans->delete();
            $status = 'success';
            $message= __('global-message.delete_form', ['form' => __('goods-cost.title')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message, 'datatable_reload' => 'dataTable_wrapper']);
        }

        return redirect()->back()->with($status, $message);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\GoodsPurchaseDataTable;
use App\Models\TransactionIn;
use App\Models\MasterAccount;
use App\Helpers\AuthHelper;
use App\Http\Requests\TransactionInRequest;

class GoodsPurchaseController extends Controller
{
    /**
     * Display a list of the Trans In.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GoodsPurchaseDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title',['form' => trans('goods-purchase.title')] );
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="'.route('goods.purchase.create').'" class="btn btn-sm btn-primary" role="button">Add Purchase</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $acc_from = MasterAccount::whereIn('code', ["2000"])->pluck('name', 'id');
        $acc_to = MasterAccount::whereIn('category_id', [1, 4])->pluck('name', 'id');

        return view('goods-purchase.form', compact('acc_from', 'acc_to'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionInRequest $request)
    {
        $req = $request->all();
        $data = [
            "trans_date" => $req['trans_date'],
            "receive_from" => $req['receive_from'],
            "store_to" => $req['store_to'],
            "value" => !empty($req['value']) ? $req['value'] : 0,
            "reference" => $req['reference'],
            "description" => $req['description']
        ];
        $trans = TransactionIn::create($data);
        // insert auto
        $acc1 = MasterAccount::whereIn('code', ["2000"])->first();  // pembelian
        $acc2 = MasterAccount::whereIn('code', ["7001"])->first();  // pembelian bersih total
        $data2 = [
            "trans_date" => $req['trans_date'],
            "receive_from" => $acc2->id,
            "store_to" => $acc1->id,
            "value" => !empty($req['value']) ? $req['value'] : 0,
            "reference" => $req['reference'],
            "description" => $req['description']
        ];
        $trans2 = TransactionIn::create($data2);

        return redirect()->route('goods.purchase.index')->withSuccess(__('message.msg_added',['name' => __('goods-purchase.title')]));
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
        // return view('goods-purchase.view', compact('data'));
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

        $acc_from = MasterAccount::whereIn('code', ["2000"])->pluck('name', 'id');
        $acc_to = MasterAccount::whereIn('category_id', [1, 4])->pluck('name', 'id');

        return view('goods-purchase.form', compact('data', 'id', 'acc_from', 'acc_to'));
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
            return redirect()->route('goods.purchase.index')->withSuccess(__('message.msg_updated',['name' => __('goods-purchase.title')]));
        }
        return redirect()->back()->withSuccess(__('message.msg_updated',['name' => __('goods-purchase.title')]));
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
        $message= __('global-message.delete_form', ['form' => __('goods-purchase.title')]);

        if ($trans != '') {
            $trans->delete();
            $status = 'success';
            $message= __('global-message.delete_form', ['form' => __('goods-purchase.title')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message, 'datatable_reload' => 'dataTable_wrapper']);
        }

        return redirect()->back()->with($status, $message);
    }
}

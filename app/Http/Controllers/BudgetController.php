<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\BudgetDataTable;
use App\Models\MasterAccount;
use App\Models\Budget;
use App\Helpers\AuthHelper;
use App\Http\Requests\BudgetRequest;

class BudgetController extends Controller
{
    /**
     * Display a list of the Data
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BudgetDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title',['form' => trans('budgets.title')] );
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="'.route('budget.create').'" class="btn btn-sm btn-primary" role="button">Add Budget</a>';
        return $dataTable->render('global.datatable', compact('pageTitle','auth_user','assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $acc_list = MasterAccount::get()->pluck('name', 'id');

        return view('budget.form', compact('acc_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BudgetRequest $request)
    {
        //dd($request->all());
        $budget = Budget::create($request->all());

        return redirect()->route('budget.index')->withSuccess(__('message.msg_added',['name' => __('budgets.store')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Budget::findOrFail($id);
        // TODO : create view blade
        // return view('budget.view', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Budget::findOrFail($id);
        $acc_list = MasterAccount::get()->pluck('name', 'id');

        return view('budget.form', compact('data', 'id', 'acc_list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BudgetRequest $request, $id)
    {
        // dd($request->all());
        $budget = Budget::findOrFail($id);

        // Update master budget data...
        $budget->fill($request->all())->update();

        if(auth()->check()){
            return redirect()->route('budget.index')->withSuccess(__('message.msg_updated',['name' => __('message.budget')]));
        }
        return redirect()->back()->withSuccess(__('message.msg_updated',['name' => 'Budget']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $budget = Budget::findOrFail($id);
        $status = 'errors';
        $message= __('global-message.delete_form', ['form' => __('budgets.title')]);

        if ($budget != '') {
            $budget->delete();
            $status = 'success';
            $message= __('global-message.delete_form', ['form' => __('budgets.title')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message, 'datatable_reload' => 'dataTable_wrapper']);
        }

        return redirect()->back()->with($status,$message);
    }
}

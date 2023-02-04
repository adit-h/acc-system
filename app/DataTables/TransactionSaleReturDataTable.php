<?php

namespace App\DataTables;

use Illuminate\Support\Facades\DB;
use App\Models\TransactionSale;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TransactionSaleReturDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->editColumn('receiveFrom.code', function ($query) {
                return $query->receiveFrom->name ?? '-';
            })
            ->editColumn('storeTo.code', function ($query) {
                return $query->storeTo->name ?? '-';
            })
            ->editColumn('value', function ($query) {
                $res = 'Rp. ' . number_format($query->value, 2, ',', '.');
                return $res;
            })
            ->editColumn('trans_date', function ($query) {
                return date('d-m-Y', strtotime($query->trans_date));
            })
            ->editColumn('updated_at', function ($query) {
                return date('d-m-Y H:i:s', strtotime($query->updated_at));
            })
            ->filterColumn('trans_date', function ($query, $keyword) {
                $sql = "transaction_sale.trans_date like ?";
                return $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->addColumn('action', 'trans-sale-retur.action')
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\TransactionSale $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $model = TransactionSale::query()->with('receiveFrom')->with('storeTo')
            ->select(DB::raw('transaction_sale.id, transaction_sale.trans_date, transaction_sale.receive_from, transaction_sale.store_to, transaction_sale.value, transaction_sale.sale_id, transaction_sale.reference, transaction_sale.description, transaction_sale.updated_at, u.first_name'))
            ->join('master_accounts AS maf', 'maf.id', 'transaction_sale.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 'transaction_sale.store_to')
            ->join('users as u', 'u.id', 'transaction_sale.updateby')
            ->orWhere(function ($query) {
                $query->whereIn('transaction_sale.receive_from', [32])
                    ->whereIn('mat.category_id', [1]);
            })
            ->orWhere(function ($query) {
                $query->whereIn('transaction_sale.receive_from', [31])
                    ->whereIn('mat.category_id', [6]);
            });
        return $this->applyScopes($model);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('dataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('<"row align-items-center"<"col-md-2" l><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>><"clear">')
            ->parameters([
                "processing" => true,
                "autoWidth" => false,
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'id', 'name' => 'id', 'title' => 'id'],
            ['data' => 'trans_date', 'name' => 'trans_date', 'title' => 'Trans Date'],
            ['data' => 'receiveFrom.code', 'name' => 'receiveFrom.code', 'title' => 'Debet'],
            ['data' => 'storeTo.code', 'name' => 'storeTo.code', 'title' => 'Credit'],
            ['data' => 'value', 'name' => 'value', 'title' => 'Value'],
            ['data' => 'reference', 'name' => 'reference', 'title' => 'Ref Number'],
            ['data' => 'description', 'name' => 'description', 'title' => 'Description'],
            ['data' => 'first_name', 'name' => 'first_name', 'title' => 'Update By'],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Last Update'],
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->width(60)
                ->addClass('text-center hide-search'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'TransactionSaleRetur_' . date('YmdHis');
    }
}

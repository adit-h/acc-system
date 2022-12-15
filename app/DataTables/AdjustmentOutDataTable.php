<?php

namespace App\DataTables;

use Illuminate\Support\Facades\DB;
use App\Models\TransactionOut;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AdjustmentOutDataTable extends DataTable
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
            ->editColumn('receiveFrom.code', function($query) {
                return $query->receiveFrom->name ?? '-';
            })
            ->editColumn('storeTo.code', function($query) {
                return $query->storeTo->name ?? '-';
            })
            ->editColumn('value', function($query) {
                $res = 'Rp. '. number_format($query->value, 0, ',', '.');
                return $res;
            })
            ->editColumn('trans_date', function($query) {
                return date('d-m-Y', strtotime($query->trans_date));
            })
            ->filterColumn('trans_date', function($query, $keyword) {
                $sql = "transaction_out.trans_date like ?";
                return $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->addColumn('action', 'adjustment-out.action')
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\TransactionOut $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $model = TransactionOut::query()->with('receiveFrom')->with('storeTo');
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
        return 'AdjustmentOut_' . date('YmdHis');
    }
}

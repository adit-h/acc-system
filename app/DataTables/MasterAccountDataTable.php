<?php

namespace App\DataTables;

use App\Models\MasterAccount;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MasterAccountDataTable extends DataTable
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
            ->editColumn('accountCategory.name', function($query) {
                return $query->accountCategory->name ?? '-';
            })
            ->editColumn('status', function($query) {
                $label = 'warning';
                switch ($query->status) {
                    case 'active':
                        $label = 'primary';
                        break;
                    case 'inactive':
                        $label = 'danger';
                        break;
                }
                return '<span class="text-capitalize badge bg-'.$label.'">'.$query->status.'</span>';
            })
            ->editColumn('created_at', function($query) {
                return date('Y/m/d',strtotime($query->created_at));
            })
            ->filterColumn('name', function($query, $keyword) {
                $sql = "master_accounts.name like ?";
                return $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->addColumn('action', 'masters.action')
            ->rawColumns(['action','status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\MasterAccount $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $model = MasterAccount::query()->with('accountCategory');
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
            ['data' => 'code', 'name' => 'code', 'title' => 'Code'],
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
            ['data' => 'accountCategory.name', 'name' => 'accountCategory.name', 'title' => 'Category'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Create Date'],
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
        return 'MasterAccount_' . date('YmdHis');
    }
}

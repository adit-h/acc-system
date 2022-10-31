<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use App\Models\TransactionIn;
use App\Models\TransactionOut;
//use Maatwebsite\Excel\Concerns\FromQuery;
//use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportIncomeExport implements FromView, WithColumnWidths
{
    //use Exportable;

    public function __construct(int $month, int $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        // test
        $data = TransactionIn::query()->with('receiveFrom')->with('storeTo')
            ->whereMonth('trans_date', $this->month)
            ->whereYear('trans_date', $this->year);

        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 35,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Set align center
            1    => ['alignment' => ['center']],
        ];
    }

    public function view(): View
    {
        $filter = date('F Y', strtotime($this->year.'-'.$this->month.'-01'));
        $trans_in = DB::table('transaction_in')
                ->select(DB::raw('master_accounts.id, master_accounts.code, master_accounts.name, SUM(transaction_in.value) AS value1'))
                ->join('master_accounts', 'master_accounts.id', 'transaction_in.store_to')
                ->whereYear('transaction_in.trans_date', '=', $this->year)
                ->whereMonth('transaction_in.trans_date', '=', $this->month)
                ->groupBy('master_accounts.id', 'master_accounts.code', 'master_accounts.name')
                ->get();
        $trans_out = DB::table('transaction_out')
                ->select(DB::raw('master_accounts.id, master_accounts.code, master_accounts.name, SUM(transaction_out.value) AS value1'))
                ->join('master_accounts', 'master_accounts.id', 'transaction_out.store_to')
                ->whereYear('transaction_out.trans_date', '=', $this->year)
                ->whereMonth('transaction_out.trans_date', '=', $this->month)
                ->groupBy('master_accounts.id', 'master_accounts.code', 'master_accounts.name')
                ->get();

        return view('reports.export-incomeState', compact('trans_in', 'trans_out', 'filter'));
    }
}

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

class ReportTransJournalExport implements FromView, WithColumnWidths
{
    //use Exportable;

    public function __construct(int $month, int $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    /**
     * not used
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
            'A' => 12,
            'B' => 7,
            'C' => 31,
            'D' => 14,
            'E' => 14,
            'F' => 14,
            'G' => 10,
            'H' => 40
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Set align center
            1    => ['alignment' => ['center']],
            2    => ['alignment' => ['center']],
        ];
    }

    public function view(): View
    {
        $filter = date('F Y', strtotime($this->year.'-'.$this->month.'-01'));

        $trans_in = DB::table('transaction_in AS t')
                ->select(DB::raw('t.trans_date, maf.code AS fromCode, maf.name AS fromName, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->whereYear('t.trans_date', '=', $this->year)
                ->whereMonth('t.trans_date', '=', $this->month);

        $trans = DB::table('transaction_out AS t')
            ->select(DB::raw('t.trans_date, maf.code AS fromCode, maf.name AS fromName, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->whereYear('t.trans_date', '=', $this->year)
            ->whereMonth('t.trans_date', '=', $this->month)
            ->union($trans_in)
            ->orderBy('trans_date')
            ->get();

        return view('reports.export-transJournal', compact('trans', 'filter'));
    }
}

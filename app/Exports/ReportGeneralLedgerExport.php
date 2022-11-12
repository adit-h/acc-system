<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use App\Models\MasterAccount;
//use Maatwebsite\Excel\Concerns\FromQuery;
//use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportGeneralLedgerExport implements FromView, WithColumnWidths, WithStyles
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
            'A' => 8,
            'B' => 32,
            'C' => 18,
            'D' => 18,
            'E' => 18,
            'F' => 18
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Set align center
            1    => ['alignment' => ['center']],
            2    => ['alignment' => ['center']],
            2    => ['font' => ['bold' => true]],
            'C'  => ['alignment' => ['right']],
            'D'  => ['alignment' => ['right']],
            'E'  => ['alignment' => ['right']],
            'F'  => ['alignment' => ['right']],
        ];
    }

    public function view(): View
    {
        $filter = date('F Y', strtotime($this->year.'-'.$this->month.'-01'));
        if ($this->month > 1) {
            $prev_month = $this->month - 1;
            $prev_year = $this->year;
        } else {
            $prev_month = 12;
            $prev_year = $this->year - 1;
        }

        // Query current Month Transactions
        $filter = date('F Y', strtotime($this->year.'-'.$this->month.'-01'));
        $trans_in = DB::table('transaction_in AS t')
            ->select(DB::raw('t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName, mat.id AS toId, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->whereYear('t.trans_date', '=', $this->year)
            ->whereMonth('t.trans_date', '=', $this->month);
        $trans = DB::table('transaction_out AS t')
            ->select(DB::raw('t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName, mat.id AS toId, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->whereYear('t.trans_date', '=', $this->year)
            ->whereMonth('t.trans_date', '=', $this->month)
            ->union($trans_in)
            ->orderBy('trans_date')
            ->get();

        // Query previous Month Transactions
        $filter_prev = date('F Y', strtotime($prev_year.'-'.$prev_month.'-01'));
        $trans_in_prev = DB::table('transaction_in AS t')
            ->select(DB::raw('t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName, mat.id AS toId, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->whereYear('t.trans_date', '=', $prev_year)
            ->whereMonth('t.trans_date', '=', $prev_month);
        $trans_prev = DB::table('transaction_out AS t')
            ->select(DB::raw('t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName, mat.id AS toId, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->whereYear('t.trans_date', '=', $prev_year)
            ->whereMonth('t.trans_date', '=', $prev_month)
            ->union($trans_in_prev)
            ->orderBy('trans_date')
            ->get();

        $bucket_prev = $this->initMasterContainer();
        // calculate previous month transactions
        foreach ($trans_prev as $key => $t) {
            $bucket_prev[$t->fromId]['debet'] += $t->value;
            $bucket_prev[$t->toId]['kredit'] += $t->value;
        }

        $bucket = $this->initMasterContainer();
        // lets do for All transaction
        foreach ($trans as $key => $t) {
            $bucket[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
            $bucket[$t->fromId]['debet'] += $t->value;
            $bucket[$t->toId]['kredit'] += $t->value;
        }

        return view('reports.export-generalLedger', compact('bucket', 'trans', 'filter'));
    }

    /**
     * Init Master Account array container
     */
    function initMasterContainer()
    {
        // Query Master Accounts data
        $master = MasterAccount::get();
        $bucket = [];   // Master container

        foreach ($master as $key => $m) {
            $bucket[$m->id] = array(
                'id' => $m->id,
                'code' => $m->code,
                'name' => $m->name,
                'last_balance' => 0,
                'debet' => 0,
                'kredit' => 0,
                'balance' => 0
            );
        }
        return $bucket;
    }
}

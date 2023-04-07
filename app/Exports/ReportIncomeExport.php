<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use App\Models\TransactionIn;
use App\Models\MasterAccount;
use App\Models\Report;
//use Maatwebsite\Excel\Concerns\FromQuery;
//use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportIncomeExport implements FromView, WithColumnWidths, WithStyles, WithColumnFormatting
{
    //use Exportable;
    protected $month, $year;
    protected $reportModel;

    public function __construct(int $m, int $y)
    {
        $this->month = $m;
        $this->year = $y;

        $this->reportModel = new Report();
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
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function view(): View
    {
        $in_cat = [6, 7];
        $out_cat = [8, 9];

        $filter = date('F Y', strtotime($this->year.'-'.$this->month.'-01'));
        if ($this->month > 1) {
            $prev_month = $this->month - 1;
            $prev_year = $this->year;
        } else {
            $prev_month = 12;
            $prev_year = $this->year - 1;
        }

        $date = date('Y-m-d', strtotime($this->year . '-' . $this->month . '-01'));
        $filter_prev = date('F Y', strtotime($prev_year . '-' . $prev_month . '-01'));
        $prev_date = date('Y-m-t', strtotime($prev_year . '-' . $prev_month . '-01'));

        // data of current month general ledger
        $data_cur = $this->reportModel->generalLedgerTemplate($date);
        $trans = $data_cur['trans'];
        $trans_prev = $data_cur['trans_prev'];
        // data of previous month general ledger
        $data_prev = $this->reportModel->generalLedgerTemplate($prev_date);
        $trans_last_month = $data_prev['trans'];
        $trans_prev_last_month = $data_prev['trans_prev'];

        // data of current month general ledger with open date filter
        $data_cur_open = $this->reportModel->generalLedgerTemplateOpenPrevDate($date);
        $trans_open = $data_cur_open['trans'];
        $trans_prev_open = $data_cur_open['trans_prev'];
        // data of previous month general ledger with open date filter
        $data_prev_open = $this->reportModel->generalLedgerTemplateOpenPrevDate($prev_date);
        $trans_last_month_open = $data_prev_open['trans'];
        $trans_prev_last_month_open = $data_prev_open['trans_prev'];

        // initiate data bucket
        $bucket_prev = $bucket_last_month = $bucket_prev_last_month = $this->initMasterContainer();
        // initiate data bucket for persediaan awal & akhir
        $bucket_prev_open = $bucket_last_month_open = $bucket_prev_last_month_open = $this->initMasterContainer();
        // calculate previous month transactions
        foreach ($trans_prev as $key => $t) {
            $bucket_prev[$t->fromId]['debet'] += $t->value;
            $bucket_prev[$t->toId]['kredit'] += $t->value;
        }
        foreach ($trans_prev_open as $key => $t) {
            $bucket_prev_open[$t->fromId]['debet'] += $t->value;
            $bucket_prev_open[$t->toId]['kredit'] += $t->value;
        }

        foreach ($trans_last_month as $key => $t) {
            $bucket_last_month[$t->fromId]['debet'] += $t->value;
            $bucket_last_month[$t->toId]['kredit'] += $t->value;
        }
        foreach ($trans_last_month_open as $key => $t) {
            $bucket_last_month_open[$t->fromId]['debet'] += $t->value;
            $bucket_last_month_open[$t->toId]['kredit'] += $t->value;
        }

        foreach ($trans_prev_last_month as $key => $t) {
            $bucket_prev_last_month[$t->fromId]['debet'] += $t->value;
            $bucket_prev_last_month[$t->toId]['kredit'] += $t->value;
        }
        foreach ($trans_prev_last_month_open as $key => $t) {
            $bucket_prev_last_month_open[$t->fromId]['debet'] += $t->value;
            $bucket_prev_last_month_open[$t->toId]['kredit'] += $t->value;
        }

        // income data. filter master account with account id = 6
        // switch debet & kredit position
        $in_data1 = $this->initMasterContainer(6);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $in_data1)) {
                $in_data1[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] + $bucket_prev[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data1)) {
                if ($t->fromId == 31) {
                    $in_data1[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['kredit'] - $bucket_prev[$t->fromId]['debet'];
                } else {
                    $in_data1[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
                }
            }
            if (array_key_exists($t->toId, $in_data1) && array_key_exists($t->fromId, $in_data1))  {
                //$in_data1[$t->fromId]['balance'] = $in_data1[$t->fromId]['last_balance'] + $in_data1[$t->fromId]['debet'] - $in_data1[$t->fromId]['kredit'];
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $in_data1)) {
                //$in_data1[$t->toId]['balance'] += $t->value;
                $in_data1[$t->toId]['debet'] += $t->value;
                $in_data1[$t->toId]['balance'] = $in_data1[$t->toId]['debet'] - $in_data1[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data1)) {
                $in_data1[$t->fromId]['kredit'] += $t->value;
                $in_data1[$t->fromId]['balance'] = $in_data1[$t->fromId]['debet'] - $in_data1[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $in_data1) && array_key_exists($t->fromId, $in_data1))  {
                $in_data1[$t->fromId]['balance'] = $in_data1[$t->fromId]['debet'] - $in_data1[$t->fromId]['kredit'];
            }
        }
        // income data. filter master account with account id = 7
        $in_data2 = $this->initMasterContainer(7);
        $supp = MasterAccount::where('code', "2000")->pluck('id');
        $supp_id = $supp[0];
        $begin_supp_id = 33;
        $purchase_id = 34;
        $total_purchase_prev = 0;   // bucket to store any previous purchase from jan to previous month
        $last_supp_id = 35;
        $hpp = MasterAccount::where('code', "7003")->pluck('id');
        $hpp_id = $hpp[0];

        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $in_data2)) {
                $in_data2[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] - $bucket_prev[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data2)) {
                $in_data2[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
            }
            if ($t->fromId == $hpp_id) {
                $in_data2[$hpp_id]['last_balance'] = $bucket_prev[$t->fromId]['debet'];
            }
            // TODO : Count total pembelian bersih bulan sebelumnya
            if ($t->fromId == $purchase_id) {
                //dump($t);
                $total_purchase_prev += $t->value;
            }
        }

        foreach ($trans_last_month as $key => $t) {
            if (array_key_exists($t->toId, $in_data2)) {
                $in_data2[$t->toId]['last_balance'] = $bucket_last_month[$t->toId]['debet'] - $bucket_last_month[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data2)) {
                $in_data2[$t->fromId]['last_balance'] = $bucket_last_month[$t->fromId]['debet'] - $bucket_last_month[$t->fromId]['kredit'];
            }
            if ($t->fromId == $hpp_id) {
                // TODO : overwrite with bucket_prev value
                $in_data2[$hpp_id]['last_balance'] = $bucket_prev[$t->fromId]['debet'];
            }
        }

        $deb_supp_prev = $cred_supp_prev = 0;
        foreach ($trans_prev_last_month as $key => $t) {
            $bucket_prev_last_month[$t->fromId]['last_balance'] = $bucket_prev_last_month[$t->fromId]['debet'] - $bucket_prev_last_month[$t->fromId]['kredit'];
            $bucket_prev_last_month[$t->toId]['last_balance'] = $bucket_prev_last_month[$t->toId]['debet'] - $bucket_prev_last_month[$t->toId]['kredit'];
            // TODO : lets count persediaan awal bulan sebelumnya !!! important
            if (array_key_exists($supp_id, $bucket_prev_last_month)) {
                $in_data2[$begin_supp_id]['last_balance'] = $bucket_prev_last_month[$supp_id]['last_balance'];
            }

            // handle count persediaan akhir bulan sebelumnya !!!
            if ($t->toId == $supp_id) {
                $cred_supp_prev += $t->value;
            }
            if ($t->fromId == $supp_id) {
                $deb_supp_prev += $t->value;
            }
            if ($t->toId == $supp_id || $t->fromId == $supp_id) {
                // TODO : WIP - test !!! important
                $in_data2[$last_supp_id]['last_balance'] = $deb_supp_prev - $cred_supp_prev;
                // TODO : WIP - test put count persediaan awal bulan ini here !!! important
                $in_data2[$begin_supp_id]['balance'] = $deb_supp_prev - $cred_supp_prev;
                // TODO : WIP - test put count persediaan akhit bulan ini here
                $in_data2[$last_supp_id]['balance'] = $in_data2[$last_supp_id]['last_balance'];
            }
        }

        $deb_supp = $cred_supp = 0;
        // Current month trans can be empty or not available. thus this logic wont be run through
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $in_data2)) {
                $in_data2[$t->toId]['kredit'] += $t->value;
                $in_data2[$t->toId]['balance'] = $in_data2[$t->toId]['debet'] - $in_data2[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data2)) {
                $in_data2[$t->fromId]['debet'] += $t->value;
                $in_data2[$t->fromId]['balance'] = $in_data2[$t->fromId]['debet'] - $in_data2[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $in_data2) && array_key_exists($t->fromId, $in_data2)) {
                //$in_data2[$t->fromId]['balance'] = $in_data2[$t->fromId]['debet'] - $in_data2[$t->fromId]['kredit'];
            }
        }

        // TODO : count bucket supply for current & previous month
        $deb_supp = $cred_supp = 0;
        $bucket_supply = $bucket_supply_prev = $this->initMasterContainer();
        foreach ($trans_open as $key => $t) {
            // lets loop through all trans first and create result like General ledger Report for each account
            $bucket_supply[$t->fromId]['debet'] += $t->value;
            $bucket_supply[$t->toId]['kredit'] += $t->value;

            // handle pembelian bersih total
            if ($t->toId == $purchase_id || $t->fromId == $purchase_id) {
                $in_data2[$purchase_id]['balance'] = $in_data2[$purchase_id]['debet'];
            }
        }
        foreach ($trans_prev_open as $key => $t) {
            $bucket_supply[$t->fromId]['last_balance'] = $bucket_prev_open[$t->fromId]['debet'] - $bucket_prev_open[$t->fromId]['kredit'];
            $bucket_supply[$t->toId]['last_balance'] = $bucket_prev_open[$t->toId]['debet'] - $bucket_prev_open[$t->toId]['kredit'];
        }
        // 2nd loop to count balance value
        foreach ($trans_prev_open as $key => $t) {
            $bucket_supply[$t->fromId]['balance'] = $bucket_supply[$t->fromId]['last_balance'] + $bucket_supply[$t->fromId]['debet'] - $bucket_supply[$t->fromId]['kredit'];
            $bucket_supply[$t->toId]['balance'] = $bucket_supply[$t->toId]['last_balance'] + $bucket_supply[$t->toId]['debet'] - $bucket_supply[$t->toId]['kredit'];
        }

        $in_data2[$begin_supp_id]['balance'] = $bucket_supply[$supp_id]['last_balance'];
        $in_data2[$last_supp_id]['last_balance'] = $bucket_supply[$supp_id]['last_balance'];
        $in_data2[$last_supp_id]['balance'] = $bucket_supply[$supp_id]['balance'];

        foreach ($trans_last_month_open as $key => $t) {
            // lets loop through all trans first and create result like General ledger Report for each account
            $bucket_supply_prev[$t->fromId]['debet'] += $t->value;
            $bucket_supply_prev[$t->toId]['kredit'] += $t->value;
        }
        foreach ($trans_prev_last_month_open as $key => $t) {
            $bucket_supply_prev[$t->fromId]['last_balance'] = $bucket_prev_last_month_open[$t->fromId]['debet'] - $bucket_prev_last_month_open[$t->fromId]['kredit'];
            $bucket_supply_prev[$t->toId]['last_balance'] = $bucket_prev_last_month_open[$t->toId]['debet'] - $bucket_prev_last_month_open[$t->toId]['kredit'];
        }
        // 2nd loop to count balance value
        foreach ($trans_prev_last_month_open as $key => $t) {
            $bucket_supply_prev[$t->fromId]['balance'] = $bucket_supply_prev[$t->fromId]['last_balance'] + $bucket_supply_prev[$t->fromId]['debet'] - $bucket_supply_prev[$t->fromId]['kredit'];
            $bucket_supply_prev[$t->toId]['balance'] = $bucket_supply_prev[$t->toId]['last_balance'] + $bucket_supply_prev[$t->toId]['debet'] - $bucket_supply_prev[$t->fromId]['kredit'];
        }
        $in_data2[$begin_supp_id]['last_balance'] = $bucket_supply_prev[$supp_id]['last_balance'];
        $in_data2[$purchase_id]['last_balance'] = $bucket_supply_prev[$purchase_id]['debet'];
        //dump($bucket_supply[6]);
        //dump($bucket_supply_prev[6]);

        // outcome data. filter master account with account id = 8
        $out_data1 = $this->initMasterContainer(8);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $out_data1)) {
                $out_data1[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] - $bucket_prev[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out_data1)) {
                $out_data1[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $out_data1) && array_key_exists($t->fromId, $out_data1))  {
                //$out_data1[$t->fromId]['balance'] = $out_data1[$t->fromId]['last_balance'] + $out_data1[$t->fromId]['debet'] - $out_data1[$t->fromId]['kredit'];
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $out_data1)) {
                //$out_data1[$t->toId]['balance'] += $t->value;
                $out_data1[$t->toId]['kredit'] += $t->value;
                $out_data1[$t->toId]['balance'] = $out_data1[$t->toId]['debet'] - $out_data1[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out_data1)) {
                $out_data1[$t->fromId]['debet'] += $t->value;
                $out_data1[$t->fromId]['balance'] = $out_data1[$t->fromId]['debet'] - $out_data1[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $out_data1) && array_key_exists($t->fromId, $out_data1)) {
                $out_data1[$t->fromId]['balance'] = $out_data1[$t->fromId]['debet'] - $out_data1[$t->fromId]['kredit'];
            }
        }
        // outcome data. filter master account with account id = 9
        $out_data2 = $this->initMasterContainer(9);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $out_data2)) {
                $out_data2[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] - $bucket_prev[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out_data2)) {
                $out_data2[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $out_data2) && array_key_exists($t->fromId, $out_data2))  {
                //$out_data2[$t->fromId]['balance'] = $out_data2[$t->fromId]['last_balance'] + $out_data2[$t->fromId]['debet'] - $out_data2[$t->fromId]['kredit'];
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $out_data2)) {
                $out_data2[$t->toId]['kredit'] += $t->value;
                $out_data2[$t->toId]['balance'] = $out_data2[$t->toId]['debet'] - $out_data2[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out_data2)) {
                $out_data2[$t->fromId]['debet'] += $t->value;
                $out_data2[$t->fromId]['balance'] = $out_data2[$t->fromId]['debet'] - $out_data2[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $out_data2) && array_key_exists($t->fromId, $out_data2))  {
                $out_data2[$t->fromId]['balance'] = $out_data2[$t->fromId]['debet'] - $out_data2[$t->fromId]['kredit'];
            }
        }

        return view('reports.export-incomeState', compact('in_data1', 'in_data2', 'out_data1', 'out_data2', 'filter', 'filter_prev',));
    }

    function initMasterContainer($catid = null)
    {
        // Query Master Accounts data
        $master = MasterAccount::get();
        if ($catid > 0) {
            $master = MasterAccount::where('category_id', $catid)->get();
        }
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

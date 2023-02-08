<?php

namespace App\Models;

use Hamcrest\Arrays\IsArray;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $yearTrans = [];
    protected $dashboardData = [];


    public function generalLedgerTemplate($date)
    {
        $res = [];
        //dump($date);

        if (!empty($date)) {
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));
            if ($month > 1) {
                $prev_month = $month - 1;
                $prev_year = $year;
            } else {
                $prev_month = 12;
                $prev_year = $year - 1;
            }

            $filter = date('F Y', strtotime($year . '-' . $month . '-01'));
            $trans_in = DB::table('transaction_in AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->whereYear('t.trans_date', '=', $year)
                ->whereMonth('t.trans_date', '=', $month);
            $trans_sale = DB::table('transaction_sale AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->whereYear('t.trans_date', '=', $year)
                ->whereMonth('t.trans_date', '=', $month);
            $trans = DB::table('transaction_out AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->whereYear('t.trans_date', '=', $year)
                ->whereMonth('t.trans_date', '=', $month)
                ->union($trans_sale)
                ->union($trans_in)
                ->orderBy('trans_date')
                ->get();

            // Query all trans to previous Month
            $filter_prev = date('F Y', strtotime($prev_year . '-' . $prev_month . '-01'));
            $prev_date = date('Y-m-t', strtotime($prev_year . '-' . $prev_month . '-01'));
            $trans_in_prev = DB::table('transaction_in AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->where('t.trans_date', '<=', $prev_date);
            $trans_sale_prev = DB::table('transaction_sale AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->where('t.trans_date', '<=', $prev_date);
            $trans_prev = DB::table('transaction_out AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->where('t.trans_date', '<=', $prev_date)
                ->union($trans_in_prev)
                ->union($trans_sale_prev)
                ->orderBy('trans_date')
                ->get();

            $res = [
                'trans' => $trans,
                'trans_prev' => $trans_prev
            ];
        }

        return $res;
    }

    public function getMonthlyTrans($date = null)
    {
        $res = [];
        // get ALL trans on current year as default
        $year = date('Y');
        $month = date('m');
        if (!empty($date)) {
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));
        }

        $trans_in = DB::table('transaction_in AS t')
            ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->whereYear('t.trans_date', '=', $year)
            ->whereMonth('t.trans_date', '=', $month);
        $trans_sale = DB::table('transaction_sale AS t')
            ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->whereYear('t.trans_date', '=', $year)
            ->whereMonth('t.trans_date', '=', $month);
        $trans = DB::table('transaction_out AS t')
            ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->whereYear('t.trans_date', '=', $year)
            ->whereMonth('t.trans_date', '=', $month)
            ->union($trans_sale)
            ->union($trans_in)
            ->orderBy('trans_date');
            //->get();

        $return = $trans->get();
        //dump($trans->toSql());

        return $return;
    }

    public function getYearlyTrans($date = null)
    {
        $return = [];
        // get ALL trans on current year as default
        $year = date('Y');
        $month = date('m');
        if (!empty($date)) {
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));
        }

        $trans_in = DB::table('transaction_in AS t')
            ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->whereYear('t.trans_date', '=', $year);
        $trans_sale = DB::table('transaction_sale AS t')
            ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->whereYear('t.trans_date', '=', $year);
        $trans = DB::table('transaction_out AS t')
            ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->whereYear('t.trans_date', '=', $year)
            ->union($trans_sale)
            ->union($trans_in)
            ->orderBy('trans_date');
            //->get();

        $return = $trans->get();
        //dump($trans->toSql());

        return $return;
    }

    function getBudgetTrans($date)
    {
        $return = [];

        if (!empty($date)) {
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));

            $trans = DB::table('budgets AS t')
                ->select(DB::raw('t.id, t.trans_date, ma.id AS accId, ma.code AS accCode, ma.name AS accName,
                    ma.category_id AS accCat, t.value, t.description'))
                ->join('master_accounts AS ma', 'ma.id', 't.acc_id')
                ->whereYear('t.trans_date', '=', $year)
                ->whereMonth('t.trans_date', '=', $month)
                ->orderBy('trans_date');

            $return = $trans->get();
        }

        return $return;
    }

    function calculateIncomeState()
    {
        $res = [];
        $param = '2022-12-01';  // for test or null as default year
        $trans = $this->getYearlyTrans($param);

        // initiate data bucket
        $in_data = $this->initMasterContainer();
        // calculate trans
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $in_data)) {
                //$in_data[$t->toId]['balance'] += $t->value;
                $in_data[$t->toId]['debet'] += $t->value;
                $in_data[$t->toId]['balance'] = $in_data[$t->toId]['debet'] - $in_data[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data)) {
                $in_data[$t->fromId]['kredit'] += $t->value;
                $in_data[$t->fromId]['balance'] = $in_data[$t->fromId]['debet'] - $in_data[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $in_data) && array_key_exists($t->fromId, $in_data)) {
                $in_data[$t->fromId]['balance'] = $in_data[$t->fromId]['debet'] - $in_data[$t->fromId]['kredit'];
            }
        }

        $sales_netto = 0;
        $goods_cost = 0;
        $cost = 0;
        $profit = 0;
        foreach ($in_data as $key => $d) {
            if ($d['catid'] == 6) {
                $sales_netto += $d['debet'] - $d['kredit'];
            }
            if ($d['code'] == '7003') {
                $goods_cost += $d['debet'] - $d['kredit'];
            }
            if ($d['catid'] == 8 || $d['catid'] == 9) {
                $cost += $d['kredit'] - $d['debet'];
            }
        }
        $profit = $sales_netto - $goods_cost;

        $res = [
            'total_sales' => $this->currFormatter($sales_netto),
            'total_cost' => $this->currFormatter($cost),
            'gross_profit' => $this->currFormatter($profit)
        ];

        return $res;
    }

    function initMasterContainer($catid = null)
    {
        // Query Master Accounts data
        $master = MasterAccount::get();
        if (is_array($catid)) {
            $master = MasterAccount::whereIn('category_id', $catid)->get();
        } else if ($catid > 0) {
            $master = MasterAccount::where('category_id', $catid)->get();
        }
        $bucket = [];   // Master container

        foreach ($master as $key => $m) {
            $bucket[$m->id] = array(
                'id' => $m->id,
                'code' => $m->code,
                'name' => $m->name,
                'catid' => $m->category_id,
                'last_balance' => 0,
                'debet' => 0,
                'kredit' => 0,
                'balance' => 0
            );
        }
        return $bucket;
    }

    function currFormatter($num)
    {
        $format = 0;
        if ($num < 1000000) {
            // Anything less than a million
            $format = number_format($num / 100000, 2) . 'K';
        } else if ($num < 1000000000) {
            // Anything less than a billion
            $format = number_format($num / 1000000, 2) . 'M';
        } else {
            // At least a billion
            $format = number_format($num / 1000000000, 2) . 'B';
        }

        return $format;
    }
}

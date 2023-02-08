<!DOCTYPE html>
<html>
<head>
	<title>Budget Report</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    @if (!empty($filter))
    <table id="basic-table" class="table table-bordered table-hover mb-0" role="grid">
        <thead>
            <tr>
                <th colspan="5" class="text-center">Budget</th>
            </tr>
        </thead>
        <tbody>
            <tr class="table-primary">
                <th>Account</th>
                <th>Income</th>
                <th>{{ $filter }}</th>
                <th>Budget</th>
                <th>Surplus/Minus</th>
            </tr>
            @php
                $total_in1a = $total_in2a = 0;
                $total_in1b = $total_in2b = 0;
            @endphp
            <!-- PENJUALAN -->
            @foreach ($in_data1 as $key => $t)
            @php
                $total_in1a += $t['balance'];
                $total_in2a += $t['budget'];
            @endphp
            <tr>
                <td><strong>{{ $t['code'] }}</strong></td>
                <td><strong>{{ $t['name'] }}</strong></td>
                <td class="text-end">{{ number_format($t['balance'], 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($t['budget'], 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($t['balance'] - $t['budget'], 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="table-secondary">
                <th colspan='2' class="text-center"><strong>Sales Netto</strong></th>
                <td class="text-end">{{ number_format($total_in1a, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_in2a, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_in1a - $total_in2a, 2, ',', '.') }}</td>
            </tr>
            <!-- PERSEDIAAN -->
            @foreach ($in_data2 as $key => $t)
            @php
                // Cost of Good == HPP value
                if ($t['code'] == '7003') {
                    $total_in1b = $t['balance'];
                    $total_in2b = $t['budget'];
                }
            @endphp
            <tr>
                <td><strong>{{ $t['code'] }}</strong></td>
                <td><strong>{{ $t['name'] }}</strong></td>
                <td class="text-end">{{ number_format($t['balance'], 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($t['budget'], 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($t['balance'] - $t['budget'], 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="table-secondary">
                <th colspan='2' class="text-center"><strong>Cost of Goods</strong></th>
                <td class="text-end">{{ number_format($total_in1b, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_in2b, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_in1b - $total_in2b, 2, ',', '.') }}</td>
            </tr>
            <tr class="table-secondary">
                <th colspan='2' class="text-center"><strong>Gross Profit</strong></th>
                <td class="text-end">{{ number_format($total_in1a - $total_in1b, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_in2a - $total_in2b, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format(($total_in1a - $total_in1b) - ($total_in2a - $total_in2b), 2, ',', '.') }}</td>
            </tr>
            <tr class="">
                <td colspan="5">&nbsp;</td>
            </tr>

            <tr class="table-primary">
                <th>Account</th>
                <th>Operational Cost</th>
                <th>{{ $filter }}</th>
                <th>{{ $filter_prev }}</th>
                <th>Surplus/Minus</th>
            </tr>
            @php
                $i = 0;
                $total_out1a = $total_out2a = 0;
                $total_out1b = $total_out2b = 0;
                $total_sales_cost1a = $total_sales_cost2a = 0;
                $total_adm_cost1a = $total_adm_cost2a = 0;
            @endphp
            @foreach ($out_data1 as $key => $t)
            @php
                $total_out1a += $t['balance'];
                $total_out2a += $t['budget'];

                $total_sales_cost1a += $t['balance'];
                $total_sales_cost2a += $t['budget'];
            @endphp
            <tr>
                <td><strong>{{ $t['code'] }}</strong></td>
                <td><strong>{{ $t['name'] }}</strong></td>
                <td class="text-end">{{ number_format($t['balance'], 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($t['budget'], 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($t['balance'] - $t['budget'], 2, ',', '.') }}</td>
            </tr>
            @if ($i == 8)
            <tr class="table-secondary">
                <th colspan='2' class="text-center"><strong>TOTAL SALES COST</strong></th>
                <td class="text-end">{{ number_format($total_sales_cost1a, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_sales_cost2a, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_sales_cost1a - $total_sales_cost2a, 2, ',', '.') }}</td>
            </tr>
            @endif
            @php
                if ($i > 8) {
                    $total_adm_cost1a += $t['balance'];
                    $total_adm_cost2a += $t['budget'];
                }
                $i++;
            @endphp
            @endforeach
            <tr class="table-secondary">
                <th colspan='2' class="text-center"><strong>TOTAL ADM COST</strong></th>
                <td class="text-end">{{ number_format($total_adm_cost1a, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_adm_cost2a, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_adm_cost1a - $total_adm_cost2a, 2, ',', '.') }}</td>
            </tr>
            <tr class="table-secondary">
                <th colspan='2' class="text-center"><strong>TOTAL COST</strong></th>
                <td class="text-end">{{ number_format($total_out1a, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_out2a, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_out1a - $total_out2a, 2, ',', '.') }}</td>
            </tr>
            <!-- BIAYA LAIN-LAIN -->
            @foreach ($out_data2 as $key => $t)
            @php
                $total_out1b += $t['balance'];
                $total_out2b += $t['budget'];
            @endphp
            <tr>
                <td><strong>{{ $t['code'] }}</strong></td>
                <td><strong>{{ $t['name'] }}</strong></td>
                <td class="text-end">{{ number_format($t['balance'], 0, '.', '.') }}</td>
                <td class="text-end">{{ number_format($t['budget'], 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($t['balance'] - $t['budget'], 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="table-secondary">
                <th colspan='2' class="text-center"><strong>TOTAL OTHERS</strong></th>
                <td class="text-end">{{ number_format($total_out1b, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_out2b, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_out1b - $total_out2b, 2, ',', '.') }}</td>
            </tr>
            <tr class="table-secondary">
                <th colspan='2' class="text-center"><strong>TOTAL OPERATIONAL COST</strong></th>
                <td class="text-end">{{ number_format($total_out1a + $total_out1b, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_out2a + $total_out2b, 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_out1a + $total_out1b - $total_out2a - $total_out2b, 2, ',', '.') }}</td>
            </tr>
            <tr class="">
                <td colspan="5">&nbsp;</td>
            </tr>
            <tr class="table-secondary">
                <th colspan='2' class="text-center"><strong>BUDGET OPERATIONAL</strong></th>
                <td class="text-end">{{ number_format( ($total_in1a - $total_in1b) - ($total_out1a + $total_out1b), 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format( ($total_in2a - $total_in2b) - ($total_out2a + $total_out2b), 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format( (($total_in1a - $total_in1b) - ($total_in2a - $total_in2b)) - ($total_out1a + $total_out1b - $total_out2a - $total_out2b), 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    @else
    <table class="table table-bordered">
        <thead>
            <tr>
                <th colspan="2" class="text-center"><strong>No Data</strong></th>
            </tr>
        </thead>
    </table>
    @endif
</body>
</html>

<!DOCTYPE html>
<html>
<head>
	<title>Balance Sheet Report</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    @if (!empty($filter))
    <table id="basic-table" class="table table-bordered table-hover mb-0" role="grid">
        <thead>
            <tr>
                <th colspan="4" class="text-center">Balance Sheet</th>
            </tr>
        </thead>
        <tbody>
            <tr class="table-primary">
                <th>Account</th>
                <th>Asset (AKTIVA)</th>
                <th>{{ $filter }}</th>
                <th>{{ $filter_prev }}</th>
            </tr>
            <tr>
                <td colspan="4" class="text-center"><strong>Aktiva</strong></td>
            </tr>
            @php
                $total_in1a = $total_in2a = 0;
                $total_in1b = $total_in2b = 0;
                $total_in1c = $total_in2c = 0;
            @endphp
            <!-- ASET LANCAR -->
            @foreach ($in_data1 as $key => $t)
            @php
                $total_in1a += $t['balance'];
                $total_in2a += $t['last_balance'];
            @endphp
            <tr>
                <td><strong>{{ $t['code'] }}</strong></td>
                <td><strong>{{ $t['name'] }}</strong></td>
                <td class="text-end">{{ number_format($t['balance'], 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($t['last_balance'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="table-secondary">
                <td colspan='2' class="text-center"><strong>ASET LANCAR</strong></td>
                <td class="text-end">{{ number_format($total_in1a, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_in2a, 0, ',', '.') }}</td>
            </tr>
            <!-- ASET TAK LANCAR -->
            @foreach ($in_data2 as $key => $t)
            @php
                $total_in1b += $t['balance'];
                $total_in2b += $t['last_balance'];
            @endphp
            <tr>
                <td><strong>{{ $t['code'] }}</strong></td>
                <td><strong>{{ $t['name'] }}</strong></td>
                <td class="text-end">{{ number_format($t['balance'], 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($t['last_balance'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="table-secondary">
                <td colspan='2' class="text-center"><strong>ASET TAK LANCAR</strong></td>
                <td class="text-end">{{ number_format($total_in1b, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_in2b, 0, ',', '.') }}</td>
            </tr>
            <!-- INVENTARIS & PENYUSUTAN -->
            @foreach ($in_data3 as $key => $t)
            @php
                $total_in1c += $t['balance'];
                $total_in2c += $t['last_balance'];
            @endphp
            <tr>
                <td><strong>{{ $t['code'] }}</strong></td>
                <td><strong>{{ $t['name'] }}</strong></td>
                <td class="text-end">{{ number_format($t['balance'], 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($t['last_balance'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="table-secondary">
                <td colspan='2' class="text-center"><strong>AKTIVA TETAP</strong></td>
                <td class="text-end">{{ number_format($total_in1c, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_in2c, 0, ',', '.') }}</td>
            </tr>
            @php
                $total_aktiva1 = $total_in1a + $total_in1b + $total_in1c;
                $total_aktiva2 = $total_in2a + $total_in2b + $total_in2c;
            @endphp
            <tr class="table-secondary">
                <td colspan='2' class="text-center"><strong>TOTAL AKTIVA</strong></td>
                <td class="text-end">{{ number_format($total_aktiva1, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_aktiva2, 0, ',', '.') }}</td>
            </tr>

            <tr class="">
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr class="table-primary">
                <th>Account</th>
                <th>HUTANG & MODAL (PASIVA)</th>
                <th>{{ $filter }}</th>
                <th>{{ $filter_prev }}</th>
            </tr>
            @php
                $total_out1a = $total_out2a = 0;
                $total_out1b = $total_out2b = 0;
            @endphp
            <!-- HUTANG / KEWAJIBAN -->
            @foreach ($out_data1 as $key => $t)
            @php
                $total_out1a += $t['balance'];
                $total_out2a += $t['last_balance'];
            @endphp
            <tr>
                <td><strong>{{ $t['code'] }}</strong></td>
                <td><strong>{{ $t['name'] }}</strong></td>
                <td class="text-end">{{ number_format($t['balance'], 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($t['last_balance'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="table-secondary">
                <td colspan='2' class="text-center"><strong>TOTAL HUTANG/KEWAJIBAN</strong></td>
                <td class="text-end">{{ number_format($total_out1a, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_out2a, 0, ',', '.') }}</td>
            </tr>
            <!-- MODAL / LABA DITAHAN -->
            @foreach ($out_data2 as $key => $t)
            @php
                $total_out1b += $t['balance'];
                $total_out2b += $t['last_balance'];
            @endphp
            <tr>
                <td><strong>{{ $t['code'] }}</strong></td>
                <td><strong>{{ $t['name'] }}</strong></td>
                <td class="text-end">{{ number_format($t['balance'], 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($t['last_balance'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="table-secondary">
                <td colspan='2' class="text-center"><strong>TOTAL MODAL / LABA DITAHAN</strong></td>
                <td class="text-end">{{ number_format($total_out1b, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_out2b, 0, ',', '.') }}</td>
            </tr>
            @php
                $total_pasiva1 = $total_out1a + $total_out1b;
                $total_pasiva2 = $total_out2a + $total_out2b;
            @endphp
            <tr class="table-secondary">
                <td colspan='2' class="text-center"><strong>TOTAL PASIVA</strong></td>
                <td class="text-end">{{ number_format($total_pasiva1, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_pasiva2, 0, ',', '.') }}</td>
            </tr>

            <tr class="">
                <td colspan="4">&nbsp;</td>
            </tr>
            @php
                $grand_total1 = $total_pasiva1 + $total_aktiva1;
                $grand_total2 = $total_pasiva2 + $total_aktiva2;
            @endphp
            <tr class="table-secondary">
                <th colspan='2' class="text-center"><strong>SURPLUS/MINUS</strong></th>
                <td class="text-end">{{ number_format( $grand_total1, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format( $grand_total2, 0, ',', '.') }}</td>
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

<!DOCTYPE html>
<html>
<head>
	<title>Income Statement Report</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    @if (!empty($filter))
    <table id="basic-table" class="table table-bordered mb-0" role="grid">
        <thead>
            <tr>
                <th colspan="2">Income Statement Report</th>
            </tr>
        </thead>
        <tbody>
            <tr class="table-primary">
                <th>Account</th>
                <th>Income</th>
                <th>{{ $filter }}</th>
                <th>{{ $filter_prev }}</th>
            </tr>
            @php
                $total_in1 = $total_in2 = 0;
            @endphp
            @foreach ($in_data as $key => $t)
            @php
                $total_in1 += $t['balance'];
                $total_in2 += $t['last_balance'];
            @endphp
            <tr>
                <td><strong>{{ $t['code'] }}</strong></td>
                <td><strong>{{ $t['name'] }}</strong></td>
                <td class="text-end">{{ number_format($t['balance'], 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($t['last_balance'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="table-secondary">
                <td colspan='2' class="text-center"><strong>TOTAL INCOME</strong></td>
                <td class="text-end">{{ number_format($total_in1, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_in2, 0, ',', '.') }}</td>
            </tr>
            <tr class="table-primary">
                <th>Account</th>
                <th>Cost</th>
                <th>{{ $filter }}</th>
                <th>{{ $filter_prev }}</th>
            </tr>
            @php
                $total_out1 = $total_out2 = 0;
            @endphp
            @foreach ($out_data as $key => $t)
            @php
                $total_out1 += $t['balance'];
                $total_out2 += $t['last_balance'];
            @endphp
            <tr>
                <td><strong>{{ $t['code'] }}</strong></td>
                <td><strong>{{ $t['name'] }}</strong></td>
                <td class="text-end">{{ number_format($t['balance'], 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($t['last_balance'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="table-secondary">
                <td colspan='2' class="text-center"><strong>TOTAL EXPENSES</strong></td>
                <td class="text-end">{{ number_format($total_out1, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($total_out2, 0, ',', '.') }}</td>
            </tr>
            <tr class="table-secondary">
                <td colspan='2' class="text-center"><strong>SURPLUS/MINUS</strong></td>
                <td class="text-end">{{ number_format( abs($total_in1 - $total_out1), 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format( abs($total_in2 - $total_out2), 0, ',', '.') }}</td>
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

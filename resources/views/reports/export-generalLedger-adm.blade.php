<!DOCTYPE html>
<html>
<head>
	<title>General Ledger Adm Report</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    @if (!empty($filter))
    <table id="basic-table" class="table table-bordered mb-0" role="grid">
        <thead>
            <tr>
                <th colspan="2">{{ $filter }}</th>
            </tr>
            <tr class="table-primary">
                <th class="text-center">Account</th>
                <th class="text-center">Description</th>
                <th class="text-center">Last Balance</th>
                <th class="text-center">Debet</th>
                <th class="text-center">Kredit</th>
                <th class="text-center">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total1 = $total2 = $total3 = $total4 = 0;
            @endphp
            @foreach ($bucket as $key => $m)
            @if (in_array($m['catid'], $catid) && !in_array($m['code'], $ex_code))
            @php
                $bal = $m['last_balance'] + $m['debet'] - $m['kredit'];
                if ($bal < 0) {
                    $balance = '('.number_format(abs($bal), 2, ',', '.').')';
                } else {
                    $balance = number_format($bal, 2, ',', '.');
                }
                $total1 += $m['last_balance'];
                $total2 += $m['debet'];
                $total3 += $m['kredit'];
                $total4 += $bal;
            @endphp
            <tr>
                <td><strong>{{ $m['code'] }}</strong></td>
                <td><strong>{{ $m['name'] }}</strong></td>
                <td>{{ number_format($m['last_balance'], 2, ',', '.') }}</td>
                <td>{{ number_format($m['debet'], 2, ',', '.') }}</td>
                <td>{{ number_format($m['kredit'], 2, ',', '.') }}</td>
                <td>{{ $balance }}</td>
            </tr>
            @endif
            @endforeach
            <tr class="table-secondary">
                <td class="text-center" colspan="2"><strong>TOTAL</strong></td>
                <td>Rp. {{ number_format($total1, 2, ',', '.') }}</td>
                <td>Rp. {{ number_format($total2, 2, ',', '.') }}</td>
                <td>Rp. {{ number_format($total3, 2, ',', '.') }}</td>
                <td>Rp. {{ number_format($total4, 2, ',', '.') }}</td>
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

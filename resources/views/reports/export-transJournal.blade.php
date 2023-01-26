<!DOCTYPE html>
<html>
<head>
	<title>Transaction Journal Report</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    @if (!empty($filter))
    <table id="basic-table" class="table table-bordered mb-0" role="grid">
        <thead>
            <tr>
                <th colspan="8">{{ $filter }}</th>
            </tr>
            <tr class="table-primary">
                <th>Date</th>
                <th>Account</th>
                <th>Name</th>
                <th>Debet</th>
                <th>Credit</th>
                <th>Balance</th>
                <th>No. Reff</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trans as $t)
            <tr>
                <td>{{ date('d-M-Y', strtotime($t->trans_date)) }}</td>
                <td>{{ $t->fromCode }}</td>
                <td>{{ $t->fromName }}</td>
                <td>{{ number_format($t->value, 0, ',', '.') }}</td>
                <td>0</td>
                <td>{{ number_format($t->value, 0, ',', '.') }}</td>
                <td>{{ $t->reference }}</td>
                <td>{{ $t->description }}</td>
            </tr>
            <tr>
                <td>{{ date('d-M-Y', strtotime($t->trans_date)) }}</td>
                <td>{{ $t->toCode }}</td>
                <td>{{ $t->toName }}</td>
                <td>0</td>
                <td>{{ number_format($t->value, 0, ',', '.') }}</td>
                <td>-</td>
                <td></td>
                <td></td>
            </tr>
            @endforeach

            {{--
            @foreach ($trans_out as $t)
            <tr>
                <td>{{ date('d-M-Y', strtotime($t->trans_date)) }}</td>
                <td>{{ $t->fromCode }}</td>
                <td>{{ $t->fromName }}</td>
                <td>{{ number_format($t->value, 0, ',', '.') }}</td>
                <td></td>
                <td>{{ number_format($t->value, 0, ',', '.') }}</td>
                <td>{{ $t->reference }}</td>
                <td>{{ $t->description }}</td>
            </tr>
            <tr>
                <td></td>
                <td>{{ $t->toCode }}</td>
                <td>{{ $t->toName }}</td>
                <td></td>
                <td>{{ number_format($t->value, 0, ',', '.') }}</td>
                <td>-</td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
            --}}
        </tbody>
    </table>
    @else
    <table id="basic-table" class="table table-bordered mb-0" role="grid">
        <thead>
            <tr>
                <th colspan="2">No Data</th>
            </tr>
        </thead>
    </table>
    @endif
</body>
</html>

<!DOCTYPE html>
<html>
<head>
	<title>Income Statement Report</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <table class="table table-bordered">
        <thead>
            @if (!empty($filter))
            <tr>
                <th colspan="2" class="text-center"><strong>{{ $filter }}</strong></th>
            </tr>
            @endif
            <tr class="table-primary">
                <th>Account</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @php $total_in=0 @endphp
            @foreach ($trans_in as $tin)
            <tr>
                <td>
                <div class="d-flex align-items-center">
                    <h6>{{ $tin->name }}</h6>
                </div>
                </td>
                <td>Rp. {{ number_format($tin->value1, 0, ',', '.') }}</td>
            </tr>
            @php $total_in += $tin->value1 @endphp
            @endforeach
            <tr class="">
                <td><strong>Total</strong></td>
                <td><strong>Rp. {{ number_format($total_in, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td colspan='2'></td>
            </tr>
            @php $total_out=0 @endphp
            @foreach ($trans_out as $tout)
            <tr>
                <td>
                <div class="d-flex align-items-center">
                    <h6>{{ $tout->name }}</h6>
                </div>
                </td>
                <td>Rp. {{ number_format($tout->value1, 0, ',', '.') }}</td>
            </tr>
            @php $total_out += $tout->value1 @endphp
            @endforeach
            <tr class="">
                <td><strong>Total</strong></td>
                <td><strong>Rp. {{ number_format($total_out, 0, ',', '.') }}</strong></td>
            </tr>
            @php $grand = $total_in - $total_out @endphp
            <tr>
                <td colspan='2'></td>
            </tr>
            <tr class="table-secondary">
                <td><strong>Total</strong></td>
                <td><strong>Rp. {{ number_format($grand, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>

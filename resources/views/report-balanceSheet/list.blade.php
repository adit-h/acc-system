<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title"> @lang('report.balance_sheet_title')</h4>
                    </div>
                </div>
                <div class="card-body justify-content-between">
                    {!! Form::open(['route' => ['report.balance.sheet.filter'], 'method' => 'post']) !!}
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="input-group">
                                <span class="input-group-text" id="search-input">
                                    <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3.09277 9.40421H20.9167" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M16.442 13.3097H16.4512" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M12.0045 13.3097H12.0137" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M7.55818 13.3097H7.56744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M16.442 17.1962H16.4512" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M12.0045 17.1962H12.0137" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M7.55818 17.1962H7.56744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M16.0433 2V5.29078" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M7.96515 2V5.29078" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M16.2383 3.5791H7.77096C4.83427 3.5791 3 5.21504 3 8.22213V17.2718C3 20.3261 4.83427 21.9999 7.77096 21.9999H16.229C19.175 21.9999 21 20.3545 21 17.3474V8.22213C21.0092 5.21504 19.1842 3.5791 16.2383 3.5791Z"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <input type="text" class="form-control vanila-datemonthpicker" name="date_input" placeholder="Tanggal" value="{{ !empty($date) ? $date : '' }}">
                                <button class="btn btn-primary btn-sm" type="submit" id="date-filter">Filter</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <a class="btn btn-outline-success" href="{{ route('report.balance.sheet.export.excel', ['date_input' => !empty($date) ? $date : '']) }}">
                                    Excel
                                </a>
                                <a class="btn btn-outline-success" href="{{ route('report.balance.sheet.export.pdf', ['date_input' => !empty($date) ? $date : '']) }}">
                                    PDF
                                </a>
                                <a class="btn btn-outline-info" href="{{ route('report.balance.sheet.export.html', ['date_input' => !empty($date) ? $date : '']) }}">
                                    Print
                                </a>
                            </div>
                        </div>
                        <div class="col-md-5">
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        @if (!empty($filter))
                        <table id="basic-table" class="table table-bordered table-hover mb-0" role="grid">
                            <thead>
                                <tr>
                                    <th colspan="5" class="text-center">Balance Sheet</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-primary">
                                    <th>Account</th>
                                    <th>Asset (AKTIVA)</th>
                                    <th>{{ $filter }}</th>
                                    <th>{{ $filter_prev }}</th>
                                    <th>Surplus/Minus</th>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-center"><strong>Aktiva</strong></td>
                                </tr>
                                @php
                                    $total_in1a = $total_in2a = 0;
                                    $total_in1b = $total_in2b = 0;
                                    $total_in1c = $total_in2c = 0;
                                    $bal = 0;
                                @endphp
                                <!-- ASET LANCAR -->
                                @foreach ($in_data1 as $key => $t)
                                @php
                                    $total_in1a += $t['balance'];
                                    $total_in2a += $t['last_balance'];
                                    $dev = $total_in1a - $total_in2a;
                                    if ($dev < 0) {
                                        $total_in = '('.number_format(abs($dev), 2, ',', '.').')';
                                    } else {
                                        $total_in = number_format($dev, 2, ',', '.');
                                    }
                                    $bal = $t['balance'] - $t['last_balance'];
                                    if ($bal < 0) {
                                        $balance = '('.number_format(abs($bal), 2, ',', '.').')';
                                    } else {
                                        $balance = number_format($bal, 2, ',', '.');
                                    }
                                @endphp
                                <tr>
                                    <td><strong>{{ $t['code'] }}</strong></td>
                                    <td><strong>{{ $t['name'] }}</strong></td>
                                    <td class="text-end">{{ number_format($t['balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($t['last_balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ $balance }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-secondary">
                                    <th colspan='2' class="text-center"><strong>ASET LANCAR</strong></th>
                                    <td class="text-end">{{ number_format($total_in1a, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($total_in2a, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ $total_in }}</td>
                                </tr>
                                <!-- ASET TAK LANCAR -->
                                @foreach ($in_data2 as $key => $t)
                                @php
                                    $total_in1b += $t['balance'];
                                    $total_in2b += $t['last_balance'];
                                    $dev = $total_in1b - $total_in2b;
                                    if ($dev < 0) {
                                        $total_in = '('.number_format(abs($dev), 2, ',', '.').')';
                                    } else {
                                        $total_in = number_format($dev, 2, ',', '.');
                                    }
                                    $bal = $t['balance'] - $t['last_balance'];
                                    if ($bal < 0) {
                                        $balance = '('.number_format(abs($bal), 2, ',', '.').')';
                                    } else {
                                        $balance = number_format($bal, 2, ',', '.');
                                    }
                                @endphp
                                <tr>
                                    <td><strong>{{ $t['code'] }}</strong></td>
                                    <td><strong>{{ $t['name'] }}</strong></td>
                                    <td class="text-end">{{ number_format($t['balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($t['last_balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ $balance }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-secondary">
                                    <th colspan='2' class="text-center"><strong>ASET TAK LANCAR</strong></th>
                                    <td class="text-end">{{ number_format($total_in1b, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($total_in2b, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ $total_in }}</td>
                                </tr>
                                <!-- INVENTARIS & PENYUSUTAN -->
                                @foreach ($in_data3 as $key => $t)
                                @php
                                    $total_in1c += $t['balance'];
                                    $total_in2c += $t['last_balance'];
                                    $dev = $total_in1c - $total_in2c;
                                    if ($dev < 0) {
                                        $total_in = '('.number_format(abs($dev), 2, ',', '.').')';
                                    } else {
                                        $total_in = number_format($dev, 2, ',', '.');
                                    }
                                    $bal = $t['balance'] - $t['last_balance'];
                                    if ($bal < 0) {
                                        $balance = '('.number_format(abs($bal), 2, ',', '.').')';
                                    } else {
                                        $balance = number_format($bal, 2, ',', '.');
                                    }
                                @endphp
                                <tr>
                                    <td><strong>{{ $t['code'] }}</strong></td>
                                    <td><strong>{{ $t['name'] }}</strong></td>
                                    <td class="text-end">{{ number_format($t['balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($t['last_balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ $balance }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-secondary">
                                    <th colspan='2' class="text-center"><strong>AKTIVA TETAP</strong></th>
                                    <td class="text-end">{{ number_format($total_in1c, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($total_in2c, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ $total_in }}</td>
                                </tr>
                                @php
                                    $total_aktiva1 = $total_in1a + $total_in1b + $total_in1c;
                                    $total_aktiva2 = $total_in2a + $total_in2b + $total_in2c;
                                @endphp
                                <tr class="table-secondary">
                                    <th colspan='2' class="text-center"><strong>TOTAL AKTIVA</strong></th>
                                    <td class="text-end">{{ number_format($total_aktiva1, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($total_aktiva2, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($total_aktiva1 - $total_aktiva2, 2, ',', '.') }}</td>
                                </tr>

                                <tr class="">
                                    <td colspan="4">&nbsp;</td>
                                </tr>
                                <tr class="table-primary">
                                    <th>Account</th>
                                    <th>HUTANG dan MODAL (PASIVA)</th>
                                    <th>{{ $filter }}</th>
                                    <th>{{ $filter_prev }}</th>
                                    <th>Surplus/Minus</th>
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
                                    $dev = $total_out1a - $total_out2a;
                                    if ($dev < 0) {
                                        $total_out = '('.number_format(abs($dev), 2, ',', '.').')';
                                    } else {
                                        $total_out = number_format($dev, 2, ',', '.');
                                    }
                                    $bal = $t['balance'] - $t['last_balance'];
                                    if ($bal < 0) {
                                        $balance = '('.number_format(abs($bal), 2, ',', '.').')';
                                    } else {
                                        $balance = number_format($bal, 2, ',', '.');
                                    }
                                @endphp
                                <tr>
                                    <td><strong>{{ $t['code'] }}</strong></td>
                                    <td><strong>{{ $t['name'] }}</strong></td>
                                    <td class="text-end">{{ number_format($t['balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($t['last_balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ $balance }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-secondary">
                                    <th colspan='2' class="text-center"><strong>TOTAL HUTANG/KEWAJIBAN</strong></th>
                                    <td class="text-end">{{ number_format($total_out1a, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($total_out2a, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ $total_out }}</td>
                                </tr>
                                <!-- MODAL / LABA DITAHAN -->
                                @foreach ($out_data2 as $key => $t)
                                @php
                                    $total_out1b += $t['balance'];
                                    $total_out2b += $t['last_balance'];
                                    $dev = $total_out1b - $total_out2b;
                                    if ($dev < 0) {
                                        $total_out = '('.number_format(abs($dev), 2, ',', '.').')';
                                    } else {
                                        $total_out = number_format($dev, 2, ',', '.');
                                    }
                                    $bal = $t['balance'] - $t['last_balance'];
                                    if ($bal < 0) {
                                        $balance = '('.number_format(abs($bal), 2, ',', '.').')';
                                    } else {
                                        $balance = number_format($bal, 2, ',', '.');
                                    }
                                @endphp
                                <tr>
                                    <td><strong>{{ $t['code'] }}</strong></td>
                                    <td><strong>{{ $t['name'] }}</strong></td>
                                    <td class="text-end">{{ number_format($t['balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($t['last_balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ $balance }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-secondary">
                                    <th colspan='2' class="text-center"><strong>TOTAL MODAL / LABA DITAHAN</strong></th>
                                    <td class="text-end">{{ number_format($total_out1b, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($total_out2b, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ $total_out }}</td>
                                </tr>
                                @php
                                    $total_pasiva1 = $total_out1a + $total_out1b;
                                    $total_pasiva2 = $total_out2a + $total_out2b;
                                @endphp
                                <tr class="table-secondary">
                                    <th colspan='2' class="text-center"><strong>TOTAL PASIVA</strong></th>
                                    <td class="text-end">{{ number_format($total_pasiva1, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($total_pasiva2, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($total_pasiva1 - $total_pasiva2, 2, ',', '.') }}</td>
                                </tr>

                                <tr class="">
                                    <td colspan="4">&nbsp;</td>
                                </tr>
                                @php
                                    $grand_total1 = $total_aktiva1 - $total_pasiva1;
                                    $grand_total2 = $total_aktiva2 - $total_pasiva2;
                                @endphp
                                <tr class="table-secondary">
                                    <th colspan='2' class="text-center"><strong>SURPLUS/MINUS</strong></th>
                                    <td class="text-end">{{ number_format( $grand_total1, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format( $grand_total2, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format( $grand_total1 - $grand_total2, 2, ',', '.') }}</td>
                                </tr>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title"> @lang('report.general_ledger_adm_title')</h4>
                    </div>
                </div>
                <div class="card-body justify-content-between">
                    {!! Form::open(['route' => ['report.general.ledger.adm.filter'], 'method' => 'post']) !!}
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
                                <a class="btn btn-outline-success" href="{{ route('report.general.ledger.adm.export.excel', ['date_input' => !empty($date) ? $date : '']) }}">
                                    Excel
                                </a>
                                <a class="btn btn-outline-success" href="{{ route('report.general.ledger.adm.export.pdf', ['date_input' => !empty($date) ? $date : '']) }}">
                                    PDF
                                </a>
                                <a class="btn btn-outline-info" href="{{ route('report.general.ledger.adm.export.html', ['date_input' => !empty($date) ? $date : '']) }}">
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
                                    <th colspan="6">{{ $filter }}</th>
                                </tr>
                                <tr class="table-primary">
                                    <th>Account</th>
                                    <th>Description</th>
                                    <th>Last Balance</th>
                                    <th>Debet</th>
                                    <th>Kredit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total1 = $total2 = $total3 = $total4 = 0;
                                @endphp
                                @foreach ($bucket as $key => $m)
                                @if (in_array($m['catid'], $catid))
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

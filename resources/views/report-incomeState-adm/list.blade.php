<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title"> @lang('report.income_state_adm_title')</h4>
                    </div>
                </div>
                <div class="card-body justify-content-between">
                    {!! Form::open(['route' => ['report.income.state.adm.filter'], 'method' => 'post']) !!}
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
                                <a class="btn btn-outline-success" href="{{ route('report.income.state.adm.export.excel', ['date_input' => !empty($date) ? $date : '']) }}">
                                    Excel
                                </a>
                                <a class="btn btn-outline-success" href="{{ route('report.income.state.adm.export.pdf', ['date_input' => !empty($date) ? $date : '']) }}">
                                    PDF
                                </a>
                                <a class="btn btn-outline-info" href="{{ route('report.income.state.adm.export.html', ['date_input' => !empty($date) ? $date : '']) }}">
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
                                    <th colspan="5" class="text-center">Income Statement</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-primary">
                                    <th>Account</th>
                                    <th>Income</th>
                                    <th>{{ $filter }}</th>
                                    <th>{{ $filter_prev }}</th>
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
                                    $total_in2a += $t['last_balance'];
                                @endphp
                                <tr>
                                    <td><strong>{{ $t['code'] }}</strong></td>
                                    <td><strong>{{ $t['name'] }}</strong></td>
                                    <td class="text-end">{{ number_format($t['balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($t['last_balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($t['balance'] - $t['last_balance'], 2, ',', '.') }}</td>
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
                                        $total_in2b = $t['last_balance'];
                                    }
                                @endphp
                                <tr>
                                    <td><strong>{{ $t['code'] }}</strong></td>
                                    <td><strong>{{ $t['name'] }}</strong></td>
                                    <td class="text-end">{{ number_format($t['balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($t['last_balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($t['balance'] - $t['last_balance'], 2, ',', '.') }}</td>
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
                                @if ($i <= 8)
                                    @php
                                        $total_out1a += $t['balance'];
                                        $total_out2a += $t['last_balance'];

                                        $total_sales_cost1a += $t['balance'];
                                        $total_sales_cost2a += $t['last_balance'];
                                    @endphp
                                <tr>
                                    <td><strong>{{ $t['code'] }}</strong></td>
                                    <td><strong>{{ $t['name'] }}</strong></td>
                                    <td class="text-end">{{ number_format($t['balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($t['last_balance'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($t['balance'] - $t['last_balance'], 2, ',', '.') }}</td>
                                </tr>
                                    @if ($i == 8)
                                <tr class="table-secondary">
                                    <th colspan='2' class="text-center"><strong>TOTAL SALES COST</strong></th>
                                    <td class="text-end">{{ number_format($total_sales_cost1a, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($total_sales_cost2a, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($total_sales_cost1a - $total_sales_cost2a, 2, ',', '.') }}</td>
                                </tr>
                                    @endif
                                @endif
                                @php
                                    if ($i > 8) {
                                        $total_adm_cost1a += $t['balance'];
                                        $total_adm_cost2a += $t['last_balance'];
                                    }
                                    $i++;
                                @endphp
                                @endforeach
                                <tr class="">
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr class="table-secondary">
                                    <th colspan='2' class="text-center"><strong>INCOME STATEMENT</strong></th>
                                    <td class="text-end">{{ number_format( ($total_in1a - $total_in1b) - ($total_out1a + $total_out1b), 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format( ($total_in2a - $total_in2b) - ($total_out2a + $total_out2b), 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format( (($total_in1a - $total_in1b) - ($total_in2a - $total_in2b)) - ($total_out1a + $total_out1b - $total_out2a - $total_out2b), 2, ',', '.') }}</td>
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

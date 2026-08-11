<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contract {{ $contract->contract_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; line-height: 1.6; color: #1a1a1a; padding: 30px 40px; }
        .header { text-align: center; border-bottom: 3px solid #1B3358; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { color: #1B3358; font-size: 18px; margin-bottom: 4px; letter-spacing: 1px; }
        .header .company { font-size: 12px; color: #666; margin-bottom: 4px; }
        .header .contract-no { font-size: 13px; font-weight: bold; color: #1B3358; }
        .header .date { font-size: 11px; color: #888; margin-top: 4px; }
        h2 { font-size: 13px; color: #1B3358; border-bottom: 1px solid #ccc; padding-bottom: 4px; margin-top: 22px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        table { width: 100%; border-collapse: collapse; margin: 6px 0 12px 0; }
        th { text-align: left; padding: 6px 10px; background: #f0f4f8; font-weight: 600; width: 35%; font-size: 10px; border: 1px solid #ddd; color: #333; }
        td { padding: 6px 10px; border: 1px solid #ddd; font-size: 11px; }
        .terms { margin-top: 10px; }
        .terms h2 { margin-top: 15px; }
        .terms ol { padding-left: 18px; margin-top: 6px; }
        .terms li { margin-bottom: 8px; font-size: 10.5px; line-height: 1.5; }
        .signatures { margin-top: 30px; page-break-inside: avoid; }
        .sig-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .sig-table td { border: none; padding: 10px 15px; width: 50%; vertical-align: top; }
        .sig-box { margin-bottom: 20px; }
        .sig-box p { margin-bottom: 3px; font-size: 10px; }
        .sig-line { border-top: 1px solid #333; padding-top: 4px; font-size: 9px; color: #666; margin-top: 35px; }
        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .badge-active { background: #E3F9EF; color: #0E9F6E; border: 1px solid #0E9F6E; }
        .badge-pending { background: #FFF8E1; color: #C9962C; border: 1px solid #C9962C; }
        .badge-signed { background: #E3F2FD; color: #1B3358; border: 1px solid #1B3358; }
        .footer { text-align: center; margin-top: 30px; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .stamp { text-align: center; margin-top: 20px; }
        .stamp-box { display: inline-block; border: 2px solid #1B3358; padding: 8px 20px; border-radius: 4px; }
        .stamp-box .text { font-size: 10px; color: #1B3358; font-weight: bold; letter-spacing: 1px; }
        .preamble { font-size: 11px; line-height: 1.7; margin-bottom: 16px; color: #333; }
        .preamble strong { color: #1B3358; }
    </style>
</head>
<body>

    <div class="header">
        <h1>BODA HIRE-PURCHASE AGREEMENT</h1>
        <div class="company">BodaLink Financial Services</div>
        <div class="contract-no">{{ $contract->contract_number }}</div>
        <div class="date">Date of Agreement: {{ $contract->created_at ? $contract->created_at->format('d F Y') : now()->format('d F Y') }}</div>
    </div>

    <table>
        <tr><th>Contract Number</th><td>{{ $contract->contract_number }}</td></tr>
        <tr><th>Status</th><td>
            @if($contract->status === 'fully_signed' || $contract->status === 'approved')
                <span class="status-badge badge-active">ACTIVE</span>
            @elseif($contract->status === 'partially_signed')
                <span class="status-badge badge-signed">PARTIALLY SIGNED</span>
            @else
                <span class="status-badge badge-pending">PENDING</span>
            @endif
        </td></tr>
        <tr><th>Loan Status</th><td>{{ ucfirst($loan->status) }}</td></tr>
    </table>

    <h2>1. Parties to the Agreement</h2>
    <div class="preamble">
        This Hire-Purchase Agreement is made and entered into on <strong>{{ $contract->created_at ? $contract->created_at->format('d F Y') : now()->format('d F Y') }}</strong> between:
    </div>
    <table>
        <tr><th>Owner (Lessor)</th><td>{{ $owner->name }}</td></tr>
        <tr><th>Owner Phone</th><td>{{ $owner->phone ?? '—' }}</td></tr>
        <tr><th>Owner NIDA</th><td>{{ $owner->nida ?? '—' }}</td></tr>
        <tr><th>Driver (Lessee)</th><td>{{ $driver->name }}</td></tr>
        <tr><th>Driver Phone</th><td>{{ $driver->phone ?? '—' }}</td></tr>
        <tr><th>Driver NIDA</th><td>{{ $driver->nida ?? '—' }}</td></tr>
    </table>

    <h2>2. Motorcycle Details</h2>
    <table>
        <tr><th>Plate Number</th><td>{{ $motorcycle->plate_number }}</td></tr>
        <tr><th>Make &amp; Model</th><td>{{ $motorcycle->make }} {{ $motorcycle->model }}</td></tr>
        <tr><th>Year</th><td>{{ $motorcycle->year }}</td></tr>
        <tr><th>Color</th><td>{{ $motorcycle->color ?? '—' }}</td></tr>
        <tr><th>Engine Number</th><td>{{ $motorcycle->engine_number ?? '—' }}</td></tr>
        <tr><th>Chassis Number</th><td>{{ $motorcycle->chassis_number ?? '—' }}</td></tr>
    </table>

    <h2>3. Loan Terms</h2>
    <table>
        <tr><th>Total Loan Amount</th><td>TZS {{ number_format($loan->total_amount) }}</td></tr>
        <tr><th>Weekly Installment</th><td>TZS {{ number_format($loan->weekly_installment) }}</td></tr>
        <tr><th>Loan Duration</th><td>{{ $loan->duration_weeks }} weeks</td></tr>
        <tr><th>Start Date</th><td>{{ $loan->start_date ? $loan->start_date->format('d F Y') : 'Upon contract activation' }}</td></tr>
        <tr><th>Amount Paid</th><td>TZS {{ number_format($loan->amount_paid) }}</td></tr>
        <tr><th>Balance Remaining</th><td>TZS {{ number_format($loan->balance) }}</td></tr>
    </table>

    <div class="terms">
        <h2>4. Terms &amp; Conditions</h2>

        <p class="preamble">
            <strong>I, {{ $driver->name }}, the Driver (Lessee)</strong>, hereby agree to the following terms and conditions with <strong>{{ $owner->name }}, the Owner (Lessor)</strong>, in connection with the hire-purchase of the motorcycle described above:
        </p>

        <ol>
            <li><strong>I</strong> agree to pay the Owner the total amount of <strong>TZS {{ number_format($loan->total_amount) }}</strong> in weekly installments of <strong>TZS {{ number_format($loan->weekly_installment) }}</strong> for a period of <strong>{{ $loan->duration_weeks }} weeks</strong>, starting from <strong>{{ $loan->start_date ? $loan->start_date->format('d F Y') : 'the date of contract activation' }}</strong>.</li>

            <li><strong>I</strong> shall make each payment on or before the due date. I understand that late payments will incur a penalty of 5% of the weekly installment per week overdue.</li>

            <li><strong>I</strong> acknowledge that the motorcycle remains the property of the Owner until the full loan amount is paid. Upon full payment, ownership of the motorcycle shall transfer to me.</li>

            <li><strong>I</strong> shall be responsible for all operating costs including fuel, maintenance, repairs, and insurance during the hire-purchase period. I shall not hold the Owner liable for any such expenses.</li>

            <li><strong>I</strong> shall not sub-lease, sell, transfer, or otherwise dispose of the motorcycle without the Owner's prior written consent. Any unauthorized disposal shall constitute a breach of this agreement.</li>

            <li><strong>I</strong> understand that in case of my default in payment, the Owner has the right to repossess the motorcycle without prejudice to any other remedies. I understand that any payments already made will not be refunded upon repossession.</li>

            <li><strong>I</strong> shall keep the motorcycle in good working condition and shall notify the Owner immediately of any accident, damage, theft, or mechanical issue affecting the motorcycle.</li>

            <li><strong>I</strong> shall not use the motorcycle for any illegal purpose or in any manner that violates the laws of the United Republic of Tanzania.</li>

            <li><strong>I</strong> agree that this agreement shall be governed by and construed in accordance with the laws of the United Republic of Tanzania, and any disputes shall be resolved in the competent courts of Tanzania.</li>
        </ol>

        <p class="preamble" style="margin-top:14px;">
            <strong>I, {{ $owner->name }}, the Owner (Lessor)</strong>, hereby agree to allow <strong>{{ $driver->name }}</strong> to hire-purchase the above-described motorcycle under the terms and conditions set forth in this agreement. I agree to transfer full ownership of the motorcycle to the Driver upon receipt of the full loan amount as specified herein.
        </p>
    </div>

    <div class="signatures">
        <h2>5. Signatures</h2>
        <div class="preamble" style="margin-bottom:10px;">By signing below, each party confirms that they have read, understood, and agreed to all terms and conditions of this agreement.</div>
        <table class="sig-table">
            <tr>
                <td>
                    <div class="sig-box">
                        <p><strong>Owner (Lessor):</strong></p>
                        <p>{{ $owner->name }}</p>
                        @if($contract->owner_signed_at)
                            <p style="color:#0E9F6E;font-weight:bold;">Signed: {{ $contract->owner_signed_at->format('d F Y H:i') }}</p>
                        @endif
                        <div class="sig-line">Owner Signature / Thumbprint</div>
                    </div>
                </td>
                <td>
                    <div class="sig-box">
                        <p><strong>Driver (Lessee):</strong></p>
                        <p>{{ $driver->name }}</p>
                        @if($contract->driver_signed_at)
                            <p style="color:#0E9F6E;font-weight:bold;">Signed: {{ $contract->driver_signed_at->format('d F Y H:i') }}</p>
                        @endif
                        <div class="sig-line">Driver Signature / Thumbprint</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="stamp">
        <div class="stamp-box">
            <div class="text">BODALINK FINANCIAL SERVICES</div>
            <div style="font-size:8px;color:#666;margin-top:2px;">Authorized Hire-Purchase Provider &mdash; Tanzania</div>
        </div>
    </div>

    <div class="footer">
        <p>BodaLink {{ date('Y') }} &bull; {{ $contract->contract_number }} &bull; Generated {{ $contract->created_at ? $contract->created_at->format('d M Y H:i') : now()->format('d M Y H:i') }}</p>
        <p style="margin-top:4px;">This is a legally binding document. Both parties should retain a copy.</p>
    </div>

</body>
</html>

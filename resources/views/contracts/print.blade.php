<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contract {{ $contract->contract_number }} — BodaLink</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy: #064180;
            --navy-light: #e8eef7;
            --gold: #c9a84c;
            --emerald: #059669;
            --text: #1e293b;
            --muted: #64748b;
            --border: #cbd5e1;
            --bg: #f8fafc;
        }

        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.6;
            color: var(--text);
            background: #e2e8f0;
        }

        .page-controls {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #1e293b;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }
        .page-controls .brand {
            color: #fff;
            font-family: 'Georgia', serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .page-controls .brand span { color: var(--gold); }
        .page-controls .actions { display: flex; gap: 8px; }
        .page-controls .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            transition: all 0.15s;
        }
        .page-controls .btn-print { background: var(--navy); color: #fff; }
        .page-controls .btn-print:hover { background: #053060; }
        .page-controls .btn-back { background: rgba(255,255,255,0.12); color: #fff; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .page-controls .btn-back:hover { background: rgba(255,255,255,0.2); }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 70px auto 40px;
            background: #fff;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
            padding: 48px 56px;
            position: relative;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 80px;
            font-weight: 900;
            color: rgba(6, 65, 128, 0.04);
            letter-spacing: 12px;
            text-transform: uppercase;
            pointer-events: none;
            white-space: nowrap;
            z-index: 0;
        }

        .contract-content { position: relative; z-index: 1; }

        .header {
            text-align: center;
            border-bottom: 3px solid var(--navy);
            padding-bottom: 20px;
            margin-bottom: 28px;
        }
        .header .logo-text {
            font-family: 'Georgia', serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header .logo-text span { color: var(--gold); }
        .header .subtitle {
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .header .contract-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--navy);
            margin-top: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header .contract-meta {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-top: 8px;
            font-size: 10px;
            color: var(--muted);
        }
        .header .contract-meta strong { color: var(--text); }

        .section { margin-bottom: 22px; }
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--navy);
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1.5px solid var(--border);
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .parties-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .party-box {
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 14px 16px;
            background: var(--bg);
        }
        .party-box .party-role {
            font-size: 9px;
            font-weight: 700;
            color: var(--navy);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .party-box .party-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
        }
        .party-box .party-detail {
            font-size: 10px;
            color: var(--muted);
            line-height: 1.8;
        }
        .party-box .party-detail strong { color: var(--text); }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 20px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-item .info-label {
            font-size: 9px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .info-item .info-value {
            font-size: 12px;
            color: var(--text);
            font-weight: 600;
        }
        .info-item .info-value.highlight { color: var(--navy); font-size: 14px; }
        .info-item .info-value.gold { color: #92710a; }

        .terms-list {
            padding-left: 18px;
            counter-reset: term;
        }
        .terms-list li {
            font-size: 11px;
            line-height: 1.7;
            margin-bottom: 7px;
            color: var(--text);
            counter-increment: term;
        }
        .terms-list li strong { color: var(--navy); }

        .payment-summary {
            border: 2px solid var(--navy);
            border-radius: 8px;
            overflow: hidden;
            margin-top: 4px;
        }
        .payment-summary .ps-header {
            background: var(--navy);
            color: #fff;
            padding: 8px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .payment-summary .ps-body {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
        .payment-summary .ps-cell {
            padding: 12px 16px;
            text-align: center;
            border-right: 1px solid var(--border);
        }
        .payment-summary .ps-cell:last-child { border-right: none; }
        .payment-summary .ps-cell .ps-label {
            font-size: 9px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .payment-summary .ps-cell .ps-value {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            margin-top: 4px;
        }

        .signatures-section {
            margin-top: 36px;
            page-break-inside: avoid;
        }
        .sig-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 20px;
        }
        .sig-box { text-align: center; }
        .sig-box .sig-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 2px;
        }
        .sig-box .sig-role {
            font-size: 9px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }
        .sig-box .sig-line {
            border-top: 1.5px solid var(--text);
            width: 80%;
            margin: 0 auto;
            padding-top: 6px;
        }
        .sig-box .sig-date {
            font-size: 9px;
            color: var(--muted);
            margin-top: 4px;
        }
        .sig-box .sig-check {
            margin-top: 10px;
            font-size: 10px;
            font-weight: 600;
        }
        .sig-box .sig-check.signed { color: var(--emerald); }
        .sig-box .sig-check.pending { color: var(--muted); }

        .witness-section {
            margin-top: 30px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }
        .witness-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 16px;
        }

        .footer-print {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 2px solid var(--navy);
            text-align: center;
        }
        .footer-print .f-brand {
            font-family: 'Georgia', serif;
            font-size: 11px;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: 0.5px;
        }
        .footer-print .f-brand span { color: var(--gold); }
        .footer-print .f-text {
            font-size: 9px;
            color: var(--muted);
            margin-top: 4px;
        }
        .footer-print .f-id {
            font-size: 8px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .stamp-area {
            margin-top: 16px;
            text-align: center;
        }
        .stamp-box {
            display: inline-block;
            width: 100px;
            height: 100px;
            border: 2px dashed var(--border);
            border-radius: 50%;
            line-height: 100px;
            font-size: 9px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media print {
            body { background: #fff; }
            .page-controls { display: none !important; }
            .page {
                margin: 0;
                box-shadow: none;
                width: 100%;
                min-height: auto;
                padding: 36px 44px;
            }
            .watermark { color: rgba(6, 65, 128, 0.03); }
            @page {
                size: A4;
                margin: 10mm;
            }
        }

        @media screen and (max-width: 900px) {
            .page {
                width: 100%;
                margin: 70px 8px 20px;
                padding: 24px 20px;
            }
            .parties-grid, .sig-grid, .witness-grid { grid-template-columns: 1fr; }
            .payment-summary .ps-body { grid-template-columns: 1fr; }
            .payment-summary .ps-cell { border-right: none; border-bottom: 1px solid var(--border); }
            .payment-summary .ps-cell:last-child { border-bottom: none; }
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="page-controls">
    <div class="brand">Boda<span>Link</span></div>
    <div class="actions">
        <a href="{{ route('contracts.show', $loan) }}" class="btn btn-back"><i class="bi bi-arrow-left"></i> Back</a>
        <button class="btn btn-print" onclick="window.print()"><i class="bi bi-printer"></i> Print Contract</button>
        <a href="{{ route('contracts.download', $loan) }}" class="btn btn-print" style="background:var(--emerald);"><i class="bi bi-download"></i> Download PDF</a>
    </div>
</div>

<div class="page">
    <div class="watermark">BODALINK</div>

    <div class="contract-content">

        <div class="header">
            <div class="logo-text">Boda<span>Link</span></div>
            <div class="subtitle">Financial Services &mdash; Tanzania</div>
            <div class="contract-title">Boda Hire-Purchase Agreement</div>
            <div class="contract-meta">
                <span>Contract: <strong>{{ $contract->contract_number }}</strong></span>
                <span>Generated: <strong>{{ $contract->created_at->format('d F Y') }}</strong></span>
                <span>Status: <strong>{{ ucfirst(str_replace('_', ' ', $contract->status)) }}</strong></span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">1. Parties to This Agreement</div>
            <div class="parties-grid">
                <div class="party-box">
                    <div class="party-role">Owner (Lender)</div>
                    <div class="party-name">{{ $owner->name ?? '—' }}</div>
                    <div class="party-detail">
                        <strong>Phone:</strong> {{ $owner->phone ?? '—' }}<br>
                        <strong>Email:</strong> {{ $owner->email ?? '—' }}<br>
                        <strong>NIDA:</strong> {{ $owner->nida ?? '—' }}
                    </div>
                </div>
                <div class="party-box">
                    <div class="party-role">Driver (Borrower)</div>
                    <div class="party-name">{{ $driver->name ?? '—' }}</div>
                    <div class="party-detail">
                        <strong>Phone:</strong> {{ $driver->phone ?? '—' }}<br>
                        <strong>Email:</strong> {{ $driver->email ?? '—' }}<br>
                        <strong>NIDA:</strong> {{ $driver->nida ?? '—' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">2. Boda Description</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Plate Number</span>
                    <span class="info-value highlight">{{ $motorcycle->plate_number ?? '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Make &amp; Model</span>
                    <span class="info-value">{{ $motorcycle->make ?? '—' }} {{ $motorcycle->model ?? '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Year</span>
                    <span class="info-value">{{ $motorcycle->year ?? '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Color</span>
                    <span class="info-value">{{ $motorcycle->color ?? '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Engine Number</span>
                    <span class="info-value">{{ $motorcycle->engine_number ?? '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Chassis Number</span>
                    <span class="info-value">{{ $motorcycle->chassis_number ?? '—' }}</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">3. Loan Terms &amp; Payment Summary</div>
            <div class="payment-summary">
                <div class="ps-header">Payment Overview</div>
                <div class="ps-body">
                    <div class="ps-cell">
                        <div class="ps-label">Total Amount</div>
                        <div class="ps-value">TZS {{ number_format($loan->total_amount) }}</div>
                    </div>
                    <div class="ps-cell">
                        <div class="ps-label">Weekly Installment</div>
                        <div class="ps-value">TZS {{ number_format($loan->weekly_installment) }}</div>
                    </div>
                    <div class="ps-cell">
                        <div class="ps-label">Duration</div>
                        <div class="ps-value">{{ $loan->duration_weeks }} Weeks</div>
                    </div>
                </div>
            </div>

            <div class="info-grid" style="margin-top:12px;">
                <div class="info-item">
                    <span class="info-label">Agreement Start Date</span>
                    <span class="info-value">{{ $loan->start_date ? $loan->start_date->format('d F Y') : '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Agreement End Date</span>
                    <span class="info-value">{{ $loan->end_date ? $loan->end_date->format('d F Y') : '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Amount Paid to Date</span>
                    <span class="info-value" style="color:var(--emerald);">TZS {{ number_format($loan->amount_paid) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Outstanding Balance</span>
                    <span class="info-value gold">TZS {{ number_format($loan->balance) }}</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">4. Terms &amp; Conditions</div>
            <ol class="terms-list">
                <li>I, the Owner, agree to provide the boda described in Section 2 to the Driver for boda transport operations within the United Republic of Tanzania.</li>
                <li>I, the Driver, agree to pay the Owner the total amount of <strong>TZS {{ number_format($loan->total_amount) }}</strong> in weekly installments of <strong>TZS {{ number_format($loan->weekly_installment) }}</strong> for a period of <strong>{{ $loan->duration_weeks }} weeks</strong>, commencing on <strong>{{ $loan->start_date ? $loan->start_date->format('d F Y') : '—' }}</strong>.</li>
                <li>Each weekly payment shall be made on or before the agreed due date. Late payments shall incur a penalty of five percent (5%) of the weekly installment per week overdue.</li>
                <li>The boda shall remain the property of the Owner until the total loan amount is fully repaid. Upon complete payment, ownership of the boda shall automatically transfer to the Driver.</li>
                <li>I, the Driver, shall be solely responsible for all operating costs including but not limited to fuel, maintenance, repairs, insurance, and any regulatory fees during the hire-purchase period.</li>
                <li>I, the Driver, shall not sub-lease, sell, mortgage, or otherwise dispose of the boda without the prior written consent of the Owner.</li>
                <li>In the event of default (defined as two or more consecutive missed weekly payments), the Owner reserves the right to repossess the boda. Any payments already made shall not be refunded, except at the Owner's discretion.</li>
                <li>I, the Driver, shall keep the boda in good working condition and shall immediately notify the Owner of any accident, theft, damage, or mechanical failure.</li>
                <li>Either party may terminate this agreement by providing at least two (2) weeks' written notice to the other party. Outstanding balances remain payable upon termination.</li>
                <li>This agreement shall be governed by and construed in accordance with the laws of the United Republic of Tanzania. Any dispute arising from this agreement shall be resolved through negotiation, and if necessary, through the competent courts of Tanzania.</li>
            </ol>
        </div>

        <div class="section signatures-section">
            <div class="section-title">5. Signatures &amp; Acknowledgement</div>
            <p style="font-size:10px;color:var(--muted);margin-bottom:4px;">By signing below, both parties acknowledge that they have read, understood, and agree to all terms and conditions set forth in this agreement.</p>

            <div class="sig-grid">
                <div class="sig-box">
                    <div class="sig-name">{{ $owner->name ?? '________________' }}</div>
                    <div class="sig-role">Owner / Lender</div>
                    <div class="sig-line"></div>
                    <div class="sig-date">Date: ____________________</div>
                    <div class="sig-check {{ $contract->isOwnerSigned() ? 'signed' : 'pending' }}">
                        @if($contract->isOwnerSigned())
                            <i class="bi bi-check-circle-fill"></i> Signed {{ $contract->owner_signed_at->format('d M Y') }}
                        @else
                            Awaiting Signature
                        @endif
                    </div>
                </div>
                <div class="sig-box">
                    <div class="sig-name">{{ $driver->name ?? '________________' }}</div>
                    <div class="sig-role">Driver / Borrower</div>
                    <div class="sig-line"></div>
                    <div class="sig-date">Date: ____________________</div>
                    <div class="sig-check {{ $contract->isDriverSigned() ? 'signed' : 'pending' }}">
                        @if($contract->isDriverSigned())
                            <i class="bi bi-check-circle-fill"></i> Signed {{ $contract->driver_signed_at->format('d M Y') }}
                        @else
                            Awaiting Signature
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="witness-section">
            <div class="section-title">6. Witness</div>
            <div class="witness-grid">
                <div>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Witness Name</span>
                            <span class="info-value">________________________</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Witness NIDA</span>
                            <span class="info-value">________________________</span>
                        </div>
                    </div>
                    <div style="margin-top:20px;text-align:center;">
                        <div style="border-top:1.5px solid var(--text);width:80%;margin:0 auto;padding-top:6px;">
                            <div style="font-size:9px;color:var(--muted);">Signature</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="stamp-area">
                        <div class="stamp-box">Official<br>Stamp</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-print">
            <div class="f-brand">Boda<span>Link</span> Financial Services</div>
            <div class="f-text">This document was generated electronically and is valid without a physical signature where accepted by both parties.</div>
            <div class="f-id">{{ $contract->contract_number }} &mdash; Generated {{ $contract->created_at->format('d M Y \a\t H:i') }}</div>
        </div>

    </div>
</div>

</body>
</html>

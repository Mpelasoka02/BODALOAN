<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Ownership - {{ $loan->motorcycle->plate_number ?? '' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #F5F7FA; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .certificate-wrapper { width: 100%; max-width: 700px; }
        .certificate {
            background: #fff;
            border: 3px solid #0F1B2D;
            border-radius: 4px;
            padding: 50px 45px;
            position: relative;
        }
        .certificate::before {
            content: '';
            position: absolute;
            top: 10px; left: 10px; right: 10px; bottom: 10px;
            border: 1px solid #E2E5EA;
            border-radius: 2px;
            pointer-events: none;
        }
        .cert-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .cert-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        .cert-brand-icon {
            width: 48px; height: 48px;
            background: #0F1B2D;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
        }
        .cert-brand-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0F1B2D;
        }
        .cert-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0F1B2D;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 6px;
        }
        .cert-divider {
            width: 120px;
            height: 3px;
            background: linear-gradient(90deg, #0F1B2D, #1B3358);
            margin: 0 auto 24px;
            border-radius: 2px;
        }
        .cert-body { text-align: center; }
        .cert-body .certifies {
            font-size: 0.95rem;
            color: #6B7684;
            margin-bottom: 10px;
        }
        .cert-body .driver-name {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0F1B2D;
            border-bottom: 2px solid #1B3358;
            display: inline-block;
            padding-bottom: 4px;
            margin-bottom: 14px;
        }
        .cert-body .ownership-text {
            font-size: 0.95rem;
            color: #6B7684;
            margin-bottom: 24px;
        }
        .cert-details {
            background: #F5F7FA;
            border: 1px solid #E2E5EA;
            border-radius: 10px;
            padding: 24px;
            margin: 0 auto 24px;
            max-width: 520px;
            text-align: left;
        }
        .cert-details h4 {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9CA3AF;
            font-weight: 700;
            margin-bottom: 12px;
            text-align: center;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            border-bottom: 1px solid #E2E5EA;
            font-size: 0.85rem;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #6B7684; font-weight: 500; }
        .detail-value { color: #0F1B2D; font-weight: 700; }
        .detail-row .detail-value.success { color: #0E9F6E; }
        .cert-footer {
            text-align: center;
            margin-top: 30px;
        }
        .cert-footer .note {
            font-size: 0.8rem;
            color: #9CA3AF;
            font-style: italic;
            margin-bottom: 20px;
        }
        .cert-date {
            font-size: 0.8rem;
            color: #9CA3AF;
            margin-top: 16px;
        }
        .print-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 20px auto 0;
            padding: 10px 28px;
            background: #0F1B2D;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            font-family: 'Inter', sans-serif;
        }
        .print-btn:hover { background: #1B3358; box-shadow: 0 4px 12px rgba(15,27,45,0.2); }
        @media print {
            body { background: #fff; padding: 0; }
            .certificate-wrapper { max-width: 100%; }
            .certificate { border-width: 2px; padding: 40px; box-shadow: none; }
            .print-btn { display: none !important; }
        }
    </style>
</head>
<body>

<div class="certificate-wrapper">
    <div class="certificate">
        <div class="cert-header">
            <div class="cert-brand">
                <div class="cert-brand-icon">&#128690;</div>
                <span class="cert-brand-name">BodaLink</span>
            </div>
            <h1 class="cert-title">Certificate of Ownership</h1>
            <div class="cert-divider"></div>
        </div>

        <div class="cert-body">
            <p class="certifies">This certifies that</p>
            <p class="driver-name">{{ $loan->driver->name ?? 'N/A' }}</p>
            <p class="ownership-text">is the owner of the following motorcycle</p>
        </div>

        <div class="cert-details">
            <h4>Motorcycle Details</h4>
            <div class="detail-row">
                <span class="detail-label">Plate Number</span>
                <span class="detail-value">{{ $loan->motorcycle->plate_number ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Make</span>
                <span class="detail-value">{{ $loan->motorcycle->make ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Model</span>
                <span class="detail-value">{{ $loan->motorcycle->model ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Year</span>
                <span class="detail-value">{{ $loan->motorcycle->year ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Color</span>
                <span class="detail-value">{{ $loan->motorcycle->color ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="cert-details">
            <h4>Loan Summary</h4>
            <div class="detail-row">
                <span class="detail-label">Total Loan Amount</span>
                <span class="detail-value">TZS {{ number_format($loan->total_amount) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Amount Paid</span>
                <span class="detail-value success">TZS {{ number_format($loan->amount_paid) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Completion Date</span>
                <span class="detail-value">{{ $loan->updated_at->format('M d, Y') }}</span>
            </div>
        </div>

        <div class="cert-footer">
            <p class="note">This certificate is generated upon full repayment of the loan agreement.</p>
            <div class="cert-date">Generated on {{ now()->format('M d, Y') }}</div>
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Print Certificate
    </button>
</div>

</body>
</html>

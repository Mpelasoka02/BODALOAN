<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account — BodaLink</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy-900: #0F1B2D;
            --navy-700: #1B3358;
            --navy-400: #3E5C85;
            --gold-500: #C9962C;
            --gold-100: #FBF3E2;
            --emerald-600: #0E9F6E;
            --emerald-100: #E3F9EF;
            --status-pending-bg: #FBF3E2;
            --status-pending-text: #C9962C;
            --page-bg: #F5F7FA;
            --card-bg: #FFFFFF;
            --border: #E2E5EA;
            --text: #1A2233;
            --text-secondary: #6B7684;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--page-bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .auth-nav {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: 0 clamp(20px, 4vw, 48px);
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .auth-nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--navy-900);
            font-weight: 800;
            font-size: 1.15rem;
        }

        .auth-nav-brand svg {
            width: 32px;
            height: 32px;
        }

        .auth-nav-back {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            transition: color 0.2s;
        }

        .auth-nav-back:hover {
            color: var(--navy-700);
        }

        .page-wrap {
            flex: 1;
            max-width: 720px;
            margin: 32px auto;
            padding: 0 clamp(16px, 3vw, 24px);
            width: 100%;
        }

        .status-banner {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 24px rgba(15,27,45,0.04);
        }

        .status-banner .icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .status-banner .icon.pending {
            background: var(--gold-100);
            color: var(--gold-500);
        }

        .status-banner .icon.submitted {
            background: var(--status-pending-bg);
            color: var(--gold-500);
        }

        .status-banner .icon.approved {
            background: var(--emerald-100);
            color: var(--emerald-600);
        }

        .status-banner .info h5 {
            font-weight: 700;
            font-size: 0.95rem;
            margin: 0;
        }

        .status-banner .info p {
            font-size: 0.82rem;
            color: var(--text-secondary);
            margin: 4px 0 0;
        }

        .steps {
            display: flex;
            justify-content: center;
            gap: 0;
            margin-bottom: 28px;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .step-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.82rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .step-circle.done {
            background: var(--emerald-600);
            color: #fff;
        }

        .step-circle.active {
            background: var(--navy-700);
            color: #fff;
        }

        .step-circle.pending {
            background: var(--page-bg);
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }

        .step-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-secondary);
            white-space: nowrap;
        }

        .step-line {
            width: 48px;
            height: 2px;
            background: var(--border);
            margin: 0 6px;
        }

        .step-line.done {
            background: var(--emerald-600);
        }

        .verify-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(15,27,45,0.08);
        }

        .verify-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .verify-header strong {
            font-weight: 700;
            font-size: 0.95rem;
        }

        .verify-body {
            padding: 24px;
        }

        .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 5px;
        }

        .form-control {
            border-radius: 10px;
            border: 1.5px solid var(--border);
            padding: 10px 14px;
            font-size: 0.85rem;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
            color: var(--text);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--navy-700);
            box-shadow: 0 0 0 3px rgba(27,51,88,0.1);
        }

        .form-text {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .upload-area {
            border: 2px dashed var(--border);
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: var(--page-bg);
            position: relative;
        }

        .upload-area:hover {
            border-color: var(--navy-700);
            background: #EEF2F7;
        }

        .upload-area.has-file {
            border-color: var(--emerald-600);
            background: var(--emerald-100);
            border-style: solid;
        }

        .upload-area input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .upload-area .upload-icon {
            font-size: 1.8rem;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .upload-area .upload-text {
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .upload-area .upload-hint {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .upload-area.has-file .upload-icon {
            color: var(--emerald-600);
        }

        .upload-area.has-file .upload-text {
            color: var(--emerald-600);
            font-weight: 600;
        }

        .preview-img {
            max-width: 100%;
            max-height: 180px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid var(--border);
            margin-top: 10px;
        }

        .preview-img.rounded {
            border-radius: 50%;
            width: 100px;
            height: 100px;
        }

        .current-doc {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
            padding: 8px 12px;
            background: var(--page-bg);
            border-radius: 10px;
        }

        .current-doc img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 1px solid var(--border);
        }

        .current-doc img.rect {
            border-radius: 6px;
            width: 56px;
            height: 40px;
        }

        .current-doc .label {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .current-doc .value {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text);
        }

        .btn-verify {
            width: 100%;
            background: var(--gold-500);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-verify:hover {
            background: #B8872A;
            box-shadow: 0 4px 14px rgba(201,150,44,0.35);
            transform: translateY(-1px);
        }

        .btn-verify:active {
            transform: translateY(0);
        }

        .alert-custom {
            border-radius: 10px;
            font-size: 0.82rem;
            padding: 12px 16px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 18px;
        }

        .alert-custom.warning {
            background: var(--gold-100);
            color: #92400E;
            border-left: 4px solid var(--gold-500);
        }

        .alert-custom.success {
            background: var(--emerald-100);
            color: #065F46;
            border-left: 4px solid var(--emerald-600);
        }

        .alert-custom.danger {
            background: #FEF2F2;
            color: #991B1B;
            border-left: 4px solid #DC2626;
        }

        .alert-custom.info {
            background: #EFF6FF;
            color: #1E40AF;
            border-left: 4px solid #2563EB;
        }

        .nida-boxes {
            display: flex;
            gap: 2px;
            flex-wrap: nowrap;
            justify-content: center;
        }

        .nida-box {
            width: 28px;
            height: 38px;
            border: 2px solid var(--border);
            border-radius: 6px;
            text-align: center;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: 'Inter', monospace;
            color: var(--text);
            background: var(--page-bg);
            outline: none;
            transition: all 0.15s ease;
            caret-color: var(--navy-700);
            padding: 0;
        }

        .nida-box:focus {
            border-color: var(--navy-700);
            box-shadow: 0 0 0 2px rgba(27,51,88,0.12);
            background: var(--card-bg);
        }

        .nida-box.filled {
            border-color: var(--navy-700);
            background: #EEF2F7;
        }

        .nida-separator {
            width: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .auth-footer-bottom {
            padding: 20px;
            text-align: center;
            font-size: 0.78rem;
            color: var(--text-secondary);
            border-top: 1px solid var(--border);
            margin-top: auto;
        }

        .auth-footer-bottom a {
            color: var(--text-secondary);
            text-decoration: none;
        }

        @media (max-width: 640px) {
            .nida-box {
                width: 22px;
                height: 32px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <nav class="auth-nav">
        <a href="{{ url('/') }}" class="auth-nav-brand">
            <img src="{{ asset('images/logo-icon.svg') }}" alt="BodaLink" width="32" height="32">
            BodaLink
        </a>
        <a href="{{ route('dashboard') }}" class="auth-nav-back"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </nav>

    <div class="page-wrap">

        @if(session('success'))
            <div class="alert-custom success"><i class="bi bi-check-circle-fill" style="margin-top:2px;"></i><div>{{ session('success') }}</div></div>
        @endif
        @if(session('warning'))
            <div class="alert-custom warning"><i class="bi bi-exclamation-triangle-fill" style="margin-top:2px;"></i><div>{{ session('warning') }}</div></div>
        @endif

        @php $user = auth()->user(); @endphp

        @if($user->hasSubmittedVerification() && $user->approval_status === 'pending')
            <div class="status-banner">
                <div class="icon submitted"><i class="bi bi-hourglass-split"></i></div>
                <div class="info">
                    <h5>Verification Under Review</h5>
                    <p>Your documents have been submitted and are being reviewed. You will be notified once approved.</p>
                </div>
            </div>
        @elseif($user->isApproved() && $user->hasVerificationDocuments())
            <div class="status-banner">
                <div class="icon approved"><i class="bi bi-shield-fill-check"></i></div>
                <div class="info">
                    <h5>Fully Verified</h5>
                    <p>Your account is verified. You have full access to all services.</p>
                </div>
            </div>
        @else
            <div class="status-banner">
                <div class="icon pending"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="info">
                    <h5>Verification Required</h5>
                    <p>Complete your verification below to gain full access to all services.</p>
                </div>
            </div>
        @endif

        <div class="steps">
            <div class="step-item">
                <div class="step-circle {{ $user->nida ? 'done' : 'active' }}">
                    @if($user->nida)<i class="bi bi-check-lg"></i>@else 1 @endif
                </div>
                <div class="step-label" style="margin-left:8px;">NIDA</div>
            </div>
            <div class="step-line {{ $user->profile_photo ? 'done' : '' }}"></div>
            <div class="step-item">
                <div class="step-circle {{ $user->profile_photo ? 'done' : ($user->nida ? 'active' : 'pending') }}">
                    @if($user->profile_photo)<i class="bi bi-check-lg"></i>@else 2 @endif
                </div>
                <div class="step-label" style="margin-left:8px;">Photo</div>
            </div>
            <div class="step-line {{ $user->id_photo ? 'done' : '' }}"></div>
            <div class="step-item">
                <div class="step-circle {{ $user->id_photo ? 'done' : (($user->nida && $user->profile_photo) ? 'active' : 'pending') }}">
                    @if($user->id_photo)<i class="bi bi-check-lg"></i>@else 3 @endif
                </div>
                <div class="step-label" style="margin-left:8px;">ID Photo</div>
            </div>
        </div>

        <div class="verify-card">
            <div class="verify-header">
                <i class="bi bi-shield-check" style="color:var(--navy-700);font-size:1.1rem;"></i>
                <strong>Verification Documents</strong>
            </div>
            <div class="verify-body">
                @if($errors->any())
                    <div class="alert-custom danger">
                        <i class="bi bi-x-circle-fill" style="margin-top:2px;"></i>
                        <div>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @if($user->rejection_reason && !$user->hasSubmittedVerification())
                    <div class="alert-custom danger">
                        <i class="bi bi-x-circle-fill" style="margin-top:2px;"></i>
                        <div>
                            <strong>Previous rejection:</strong> {{ $user->rejection_reason }}<br>
                            <span style="font-size:0.8rem;">Please correct and resubmit your documents.</span>
                        </div>
                    </div>
                @endif

                @if($user->hasSubmittedVerification() && $user->approval_status === 'pending')
                    <div class="alert-custom info">
                        <i class="bi bi-info-circle-fill" style="margin-top:2px;"></i>
                        <div>Your documents are being reviewed. You cannot resubmit while under review.</div>
                    </div>
                @else
                    <form method="POST" action="{{ route('verification.submit') }}" enctype="multipart/form-data" id="verificationForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">NIDA Number <span style="color:#DC2626;">*</span></label>
                            <input type="hidden" name="nida" id="nidaHidden" value="{{ old('nida', $user->nida) }}">
                            <div class="nida-boxes" id="nidaBoxes">
                                @php
                                    $nidaVal = old('nida', $user->nida ?? '');
                                    $groups = [['start' => 0, 'end' => 5], ['start' => 5, 'end' => 10], ['start' => 10, 'end' => 15], ['start' => 15, 'end' => 20]];
                                    $idx = 0;
                                @endphp
                                @foreach($groups as $gIdx => $group)
                                    @if($gIdx > 0)<div class="nida-separator">&middot;</div>@endif
                                    @for($i = $group['start']; $i < $group['end']; $i++)
                                        <input type="text" class="nida-box" maxlength="1" inputmode="numeric" pattern="[0-9]"
                                            data-index="{{ $idx }}" value="{{ isset($nidaVal[$i]) ? $nidaVal[$i] : '' }}"
                                            autocomplete="off" aria-label="NIDA digit {{ $idx + 1 }}">
                                        @php $idx++; @endphp
                                    @endfor
                                @endforeach
                            </div>
                            <div class="form-text">Enter your 20-digit National Identification Authority (NIDA) number</div>
                            @if($user->nida)
                                <div class="current-doc">
                                    <i class="bi bi-check-circle-fill" style="color:var(--emerald-600);font-size:1.1rem;"></i>
                                    <div><div class="label">Current NIDA</div><div class="value">{{ $user->nida }}</div></div>
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Profile Photo <span style="color:#DC2626;">*</span></label>
                            <div class="upload-area" id="profileUploadArea">
                                <input type="file" name="profile_photo" accept="image/jpeg,image/png" id="profilePhotoInput" {{ $user->profile_photo ? '' : 'required' }}>
                                <div class="upload-icon"><i class="bi bi-camera-fill"></i></div>
                                <div class="upload-text" id="profileUploadText">Click or drag to upload profile photo</div>
                                <div class="upload-hint">JPG or PNG, max 2MB</div>
                            </div>
                            <div id="profilePreview"></div>
                            @if($user->profile_photo_url)
                                <div class="current-doc">
                                    <img src="{{ $user->profile_photo_url }}" alt="Current photo">
                                    <div><div class="label">Current profile photo</div><div class="value">Uploaded</div></div>
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label">ID Photo (NIDA Card / Passport) <span style="color:#DC2626;">*</span></label>
                            <div class="upload-area" id="idUploadArea">
                                <input type="file" name="id_photo" accept="image/jpeg,image/png" id="idPhotoInput" {{ $user->id_photo ? '' : 'required' }}>
                                <div class="upload-icon"><i class="bi bi-credit-card-2-front-fill"></i></div>
                                <div class="upload-text" id="idUploadText">Click or drag to upload ID photo</div>
                                <div class="upload-hint">Clear photo of your national ID or passport, JPG or PNG, max 2MB</div>
                            </div>
                            <div id="idPreview"></div>
                            @if($user->id_photo_url)
                                <div class="current-doc">
                                    <img src="{{ $user->id_photo_url }}" alt="Current ID" class="rect">
                                    <div><div class="label">Current ID photo</div><div class="value">Uploaded</div></div>
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="btn-verify">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                            @if($user->hasVerificationDocuments())
                                Resubmit Verification Documents
                            @else
                                Submit for Verification
                            @endif
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

<script>
    (function() {
        const boxes = document.querySelectorAll('.nida-box');
        const hidden = document.getElementById('nidaHidden');
        const total = boxes.length;

        function syncHidden() {
            let val = '';
            boxes.forEach(b => { val += b.value; });
            hidden.value = val;
            boxes.forEach(b => { b.classList.toggle('filled', b.value !== ''); });
        }

        function focusBox(idx) { if (idx >= 0 && idx < total) boxes[idx].focus(); }

        boxes.forEach((box, i) => {
            box.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1);
                if (this.value && i < total - 1) focusBox(i + 1);
                syncHidden();
            });
            box.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace') {
                    if (!this.value && i > 0) { boxes[i - 1].value = ''; focusBox(i - 1); syncHidden(); }
                } else if (e.key === 'ArrowLeft') { e.preventDefault(); focusBox(i - 1); }
                else if (e.key === 'ArrowRight') { e.preventDefault(); focusBox(i + 1); }
                else if (e.key === 'Enter') { e.preventDefault(); document.getElementById('verificationForm').requestSubmit(); }
            });
            box.addEventListener('paste', function(e) {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                for (let j = 0; j < Math.min(text.length, total - i); j++) boxes[i + j].value = text[j];
                focusBox(Math.min(i + text.length, total - 1));
                syncHidden();
            });
            box.addEventListener('focus', function() { this.select(); });
        });
        syncHidden();
    })();

    document.getElementById('profilePhotoInput').addEventListener('change', function(e) {
        const area = document.getElementById('profileUploadArea');
        const text = document.getElementById('profileUploadText');
        const preview = document.getElementById('profilePreview');
        preview.innerHTML = '';
        if (e.target.files[0]) {
            area.classList.add('has-file');
            text.textContent = e.target.files[0].name;
            const img = document.createElement('img');
            img.src = URL.createObjectURL(e.target.files[0]);
            img.className = 'preview-img rounded';
            preview.appendChild(img);
        }
    });

    document.getElementById('idPhotoInput').addEventListener('change', function(e) {
        const area = document.getElementById('idUploadArea');
        const text = document.getElementById('idUploadText');
        const preview = document.getElementById('idPreview');
        preview.innerHTML = '';
        if (e.target.files[0]) {
            area.classList.add('has-file');
            text.textContent = e.target.files[0].name;
            const img = document.createElement('img');
            img.src = URL.createObjectURL(e.target.files[0]);
            img.className = 'preview-img';
            preview.appendChild(img);
        }
    });
</script>

</body>
</html>

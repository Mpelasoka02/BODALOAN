<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Loan;
use App\Models\User;
use App\Models\UserNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function show(Loan $loan)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $loan->owner_id !== $user->id && $loan->driver_id !== $user->id) {
            abort(403);
        }

        $contract = $loan->contract;
        if (!$contract) {
            $contract = $this->generate($loan);
        }

        return view('contracts.show', compact('contract', 'loan'));
    }

    public function generate(Loan $loan)
    {
        $contract = $loan->contract;
        if (!$contract) {
            $contract = $loan->motorcycle->contract;
        }
        if ($contract) {
            $contract->update(['loan_id' => $loan->id]);
        } else {
            $contractNumber = 'CTR-' . now()->format('Y') . '-' . str_pad($loan->id, 5, '0', STR_PAD_LEFT);
            $contract = Contract::create([
                'loan_id' => $loan->id,
                'contract_number' => $contractNumber,
                'status' => 'pending',
            ]);
        }

        $pdf = Pdf::loadView('contracts.pdf', [
            'contract' => $contract,
            'loan' => $loan,
            'motorcycle' => $loan->motorcycle,
            'owner' => $loan->owner,
            'driver' => $loan->driver,
        ]);

        $path = 'contracts/contract-' . $contract->id . '.pdf';
        Storage::disk('local')->put($path, $pdf->output());
        $contract->update(['pdf_path' => $path]);

        return $contract;
    }

    public function printContract(Loan $loan)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $loan->owner_id !== $user->id && $loan->driver_id !== $user->id) {
            abort(403);
        }

        $contract = $loan->contract;
        if (!$contract) {
            $contract = $this->generate($loan);
        }

        $loan->load(['motorcycle', 'driver', 'owner']);

        return view('contracts.print', [
            'contract' => $contract,
            'loan' => $loan,
            'motorcycle' => $loan->motorcycle,
            'owner' => $loan->owner,
            'driver' => $loan->driver,
        ]);
    }

    public function downloadPdf(Loan $loan)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $loan->owner_id !== $user->id && $loan->driver_id !== $user->id) {
            abort(403);
        }

        $contract = $loan->contract;
        if (!$contract) {
            $contract = $this->generate($loan);
        }

        $loan->load(['motorcycle', 'driver', 'owner']);

        $pdf = Pdf::loadView('contracts.pdf', [
            'contract' => $contract,
            'loan' => $loan,
            'motorcycle' => $loan->motorcycle,
            'owner' => $loan->owner,
            'driver' => $loan->driver,
        ]);

        return $pdf->download('contract-' . $contract->contract_number . '.pdf');
    }

    public function showUploadForm(Loan $loan)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $loan->owner_id !== $user->id && $loan->driver_id !== $user->id) {
            abort(403);
        }

        $contract = $loan->contract;
        if (!$contract) {
            $contract = $this->generate($loan);
        }

        $role = $user->isOwner() ? 'owner' : ($user->isDriver() ? 'driver' : null);

        return view('contracts.upload', compact('contract', 'loan', 'role'));
    }

    public function uploadSigned(Request $request, Loan $loan)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $loan->owner_id !== $user->id && $loan->driver_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'signed_pdf' => 'required|mimes:pdf|max:10240',
        ]);

        $contract = $loan->contract;
        if (!$contract) {
            $contract = $this->generate($loan);
        }

        if ($contract->status !== 'approved' && $contract->status !== 'partially_signed' && $contract->status !== 'fully_signed') {
            return back()->with('error', 'The owner must approve this contract before you can sign it.');
        }

        if ($contract->status === 'fully_signed') {
            return back()->with('error', 'This contract has already been fully signed by both parties.');
        }

        $role = $user->isOwner() ? 'owner' : ($user->isDriver() ? 'driver' : null);
        if (!$role) {
            return back()->with('error', 'Only owners and drivers can upload signed contracts.');
        }

        $path = $request->file('signed_pdf')->store('contracts/signed', 'local');

        $update = [];
        if ($role === 'owner') {
            $update['owner_signed_pdf'] = $path;
            $update['owner_signed_at'] = now();
        } else {
            $update['driver_signed_pdf'] = $path;
            $update['driver_signed_at'] = now();
        }

        $contract->update($update);

        if ($contract->isOwnerSigned() && $contract->isDriverSigned()) {
            $contract->update(['status' => 'fully_signed']);

            $loan->update([
                'status' => 'active',
                'agreement_accepted_at' => now(),
                'start_date' => now(),
                'next_payment_date' => now()->addDays(7),
            ]);

            UserNotification::createNotification(
                $loan->owner_id,
                'contract_signed',
                'Contract Active',
                "Contract {$contract->contract_number} for {$loan->motorcycle->plate_number} has been signed by both parties. The loan is now active.",
                ['loan_id' => $loan->id, 'contract_id' => $contract->id]
            );

            UserNotification::createNotification(
                $loan->driver_id,
                'contract_signed',
                'Contract Active',
                "Contract {$contract->contract_number} for {$loan->motorcycle->plate_number} has been signed by both parties. The loan is now active.",
                ['loan_id' => $loan->id, 'contract_id' => $contract->id]
            );
        } elseif ($contract->isOwnerSigned() || $contract->isDriverSigned()) {
            $contract->update(['status' => 'partially_signed']);

            if ($role === 'driver') {
                UserNotification::createNotification(
                    $loan->owner_id,
                    'contract_signed_by_driver',
                    'Driver Signed Contract',
                    "Driver {$loan->driver->name} has signed contract {$contract->contract_number} for {$loan->motorcycle->plate_number}. Please review and sign.",
                    ['loan_id' => $loan->id, 'contract_id' => $contract->id]
                );

                if ($loan->owner && $loan->owner->phone) {
                    try {
                        $sms = app(\App\Services\SmsService::class);
                        $sms->send($loan->owner->phone, "BodaLink: Driver {$loan->driver->name} has signed contract {$contract->contract_number} for {$loan->motorcycle->plate_number}. Please review and sign.");
                    } catch (\Exception $e) {
                        Log::error('Failed to send driver signed SMS', ['error' => $e->getMessage()]);
                    }
                }
            } else {
                UserNotification::createNotification(
                    $loan->driver_id,
                    'contract_signed_by_owner',
                    'Owner Signed Contract',
                    "Owner {$loan->owner->name} has signed contract {$contract->contract_number} for {$loan->motorcycle->plate_number}. You can now sign it.",
                    ['loan_id' => $loan->id, 'contract_id' => $contract->id]
                );

                if ($loan->driver && $loan->driver->phone) {
                    try {
                        $sms = app(\App\Services\SmsService::class);
                        $sms->send($loan->driver->phone, "BodaLink: Owner {$loan->owner->name} has signed contract {$contract->contract_number} for {$loan->motorcycle->plate_number}. You can now sign it.");
                    } catch (\Exception $e) {
                        Log::error('Failed to send owner signed SMS', ['error' => $e->getMessage()]);
                    }
                }
            }
        }

        return redirect()->route('contracts.show', $loan)->with('success', 'Signed contract uploaded successfully.');
    }

    public function ownerContracts()
    {
        $user = Auth::user();
        $loans = Loan::where('owner_id', $user->id)
            ->with(['contract', 'driver', 'motorcycle'])
            ->latest()
            ->paginate(15);

        return view('owner.contracts', compact('loans'));
    }

    public function driverContracts()
    {
        $user = Auth::user();
        $loans = Loan::where('driver_id', $user->id)
            ->with(['contract', 'owner', 'motorcycle'])
            ->latest()
            ->paginate(15);

        return view('driver.contracts', compact('loans'));
    }

    public function ownerApproveContract(Loan $loan)
    {
        $user = Auth::user();
        if ($loan->owner_id !== $user->id) abort(403);

        $contract = $loan->contract;
        if (!$contract) abort(404);

        if ($contract->owner_approved_at) {
            return back()->with('error', 'Contract already approved.');
        }

        $contract->update([
            'owner_approved_at' => now(),
            'status' => 'approved',
            'rejection_reason' => null,
        ]);

        UserNotification::createNotification(
            $loan->driver_id,
            'contract_approved',
            'Contract Approved',
            "Contract {$contract->contract_number} for {$loan->motorcycle->plate_number} has been approved by the owner. You can now sign it.",
            ['loan_id' => $loan->id, 'contract_id' => $contract->id]
        );

        if ($loan->driver && $loan->driver->phone) {
            try {
                $sms = app(\App\Services\SmsService::class);
                $sms->send($loan->driver->phone, "BodaLink: Contract {$contract->contract_number} for {$loan->motorcycle->plate_number} has been approved by the owner. You can now sign it.");
            } catch (\Exception $e) {
                Log::error('Failed to send contract approval SMS', ['error' => $e->getMessage()]);
            }
        }

        return redirect()->route('owner.contracts')->with('success', "Contract for {$loan->motorcycle->plate_number} approved. Driver has been notified to sign.");
    }

    public function ownerRejectContract(Request $request, Loan $loan)
    {
        $user = Auth::user();
        if ($loan->owner_id !== $user->id) abort(403);

        $contract = $loan->contract;
        if (!$contract) abort(404);

        if ($contract->owner_approved_at) {
            return back()->with('error', 'Cannot reject an already approved contract.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $contract->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        $loan->update(['status' => 'cancelled']);

        $motorcycle = $loan->motorcycle;
        if ($motorcycle) {
            $motorcycle->update([
                'driver_id' => null,
                'status' => 'available',
            ]);
        }

        UserNotification::createNotification(
            $loan->driver_id,
            'contract_rejected',
            'Contract Rejected',
            "Contract {$contract->contract_number} for {$loan->motorcycle->plate_number} has been rejected by the owner. Reason: {$request->rejection_reason}",
            ['loan_id' => $loan->id, 'contract_id' => $contract->id]
        );

        return redirect()->route('owner.contracts')->with('success', 'Contract rejected. The driver has been notified.');
    }

}

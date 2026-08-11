<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Loan;
use App\Models\Motorcycle;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\VehicleLocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────────
        //  USERS
        // ──────────────────────────────────────────────

        $admin = User::create([
            'name'             => 'Super Admin',
            'email'            => 'admin@bodaloan.com',
            'phone'            => '0700000001',
            'nida'             => '1985010112345600001',
            'role'             => 'admin',
            'approval_status'  => 'approved',
            'is_active'        => true,
            'email_verified_at'=> now(),
            'password'         => Hash::make('password'),
        ]);

        $owner1 = User::create([
            'name'             => 'Saidi Bakari',
            'email'            => 'owner1@bodaloan.com',
            'phone'            => '0712345678',
            'nida'             => '1985031512345600002',
            'role'             => 'owner',
            'approval_status'  => 'approved',
            'is_active'        => true,
            'email_verified_at'=> now(),
            'password'         => Hash::make('password'),
        ]);

        $owner2 = User::create([
            'name'             => 'Fatuma Juma',
            'email'            => 'owner2@bodaloan.com',
            'phone'            => '0754889001',
            'nida'             => '1990072212345600003',
            'role'             => 'owner',
            'approval_status'  => 'approved',
            'is_active'        => true,
            'email_verified_at'=> now(),
            'password'         => Hash::make('password'),
        ]);

        $driver1 = User::create([
            'name'                    => 'Joseph Massawe',
            'email'                   => 'driver1@bodaloan.com',
            'phone'                   => '0713001002',
            'nida'                    => '1995111012345600004',
            'role'                    => 'driver',
            'approval_status'         => 'approved',
            'is_active'               => true,
            'email_verified_at'       => now(),
            'password'                => Hash::make('password'),
            'verification_submitted_at'=> now()->subDays(30),
        ]);

        $driver2 = User::create([
            'name'                    => 'John Doe',
            'email'                   => 'driver2@bodaloan.com',
            'phone'                   => '0716004005',
            'nida'                    => '1998022812345600005',
            'role'                    => 'driver',
            'approval_status'         => 'approved',
            'is_active'               => true,
            'email_verified_at'       => now(),
            'password'                => Hash::make('password'),
            'verification_submitted_at'=> now()->subDays(20),
        ]);

        $driver3 = User::create([
            'name'                    => 'Peter Shirima',
            'email'                   => 'driver3@bodaloan.com',
            'phone'                   => '0718006007',
            'nida'                    => '1996041212345600006',
            'role'                    => 'driver',
            'approval_status'         => 'pending',
            'is_active'               => false,
            'email_verified_at'       => now(),
            'password'                => Hash::make('password'),
            'verification_submitted_at'=> now()->subDays(5),
        ]);

        // ──────────────────────────────────────────────
        //  MOTORCYCLES  (8 total)
        // ──────────────────────────────────────────────

        // Owner 1 — 5 vehicles
        $m1 = Motorcycle::create([
            'owner_id'            => $owner1->id,
            'driver_id'           => null,
            'plate_number'        => 'T 452 ABC',
            'make'                => 'TVS',
            'model'               => 'HLX 125',
            'year'                => 2023,
            'color'               => 'Blue',
            'engine_cc'           => 125,
            'engine_number'       => 'EN-TVS-2023-452A',
            'chassis_number'      => 'CH-TVS-2023-452A',
            'weekly_amount'       => 50000,
            'loan_amount'         => 1500000,
            'loan_duration_weeks' => 30,
            'status'              => 'available',
            'verification_status' => 'pending_verification',
        ]);

        $m2 = Motorcycle::create([
            'owner_id'            => $owner1->id,
            'driver_id'           => null,
            'plate_number'        => 'T 781 MNO',
            'make'                => 'Bajaj',
            'model'               => 'Boxer BM150',
            'year'                => 2024,
            'color'               => 'Black',
            'engine_cc'           => 150,
            'engine_number'       => 'EN-BAJAJ-2024-781M',
            'chassis_number'      => 'CH-BAJAJ-2024-781M',
            'weekly_amount'       => 60000,
            'loan_amount'         => 2000000,
            'loan_duration_weeks' => 33,
            'status'              => 'available',
            'verification_status' => 'verified',
        ]);

        $m3 = Motorcycle::create([
            'owner_id'            => $owner1->id,
            'driver_id'           => $driver1->id,
            'plate_number'        => 'T 119 BDF',
            'make'                => 'Honda',
            'model'               => 'Livo',
            'year'                => 2023,
            'color'               => 'Red',
            'engine_cc'           => 110,
            'engine_number'       => 'EN-HONDA-2023-119B',
            'chassis_number'      => 'CH-HONDA-2023-119B',
            'gps_device_id'       => 'TRC-001-HONDA',
            'weekly_amount'       => 55000,
            'loan_amount'         => 1800000,
            'loan_duration_weeks' => 32,
            'status'              => 'assigned',
            'verification_status' => 'verified',
            'last_location_at'    => now()->subMinutes(3),
        ]);

        $m4 = Motorcycle::create([
            'owner_id'            => $owner1->id,
            'driver_id'           => $driver2->id,
            'plate_number'        => 'T 334 PQR',
            'make'                => 'Yamaha',
            'model'               => 'Fascino',
            'year'                => 2024,
            'color'               => 'White',
            'engine_cc'           => 125,
            'engine_number'       => 'EN-YAMAHA-2024-334P',
            'chassis_number'      => 'CH-YAMAHA-2024-334P',
            'gps_device_id'       => 'TRC-002-YAMAHA',
            'weekly_amount'       => 45000,
            'loan_amount'         => 1200000,
            'loan_duration_weeks' => 26,
            'status'              => 'assigned',
            'verification_status' => 'verified',
            'last_location_at'    => now()->subMinutes(12),
        ]);

        $m5 = Motorcycle::create([
            'owner_id'            => $owner1->id,
            'driver_id'           => $driver1->id,
            'plate_number'        => 'T 567 XYZ',
            'make'                => 'TVS',
            'model'               => 'Apache RTR',
            'year'                => 2022,
            'color'               => 'Black',
            'engine_cc'           => 160,
            'engine_number'       => 'EN-TVS-2022-567X',
            'chassis_number'      => 'CH-TVS-2022-567X',
            'weekly_amount'       => 40000,
            'loan_amount'         => 800000,
            'loan_duration_weeks' => 20,
            'status'              => 'completed',
            'verification_status' => 'verified',
        ]);

        // Owner 2 — 3 vehicles
        $m6 = Motorcycle::create([
            'owner_id'            => $owner2->id,
            'driver_id'           => null,
            'plate_number'        => 'T 882 CZK',
            'make'                => 'Bajaj',
            'model'               => 'Pulsar 150',
            'year'                => 2024,
            'color'               => 'Red',
            'engine_cc'           => 150,
            'engine_number'       => 'EN-BAJAJ-2024-882C',
            'chassis_number'      => 'CH-BAJAJ-2024-882C',
            'weekly_amount'       => 65000,
            'loan_amount'         => 2500000,
            'loan_duration_weeks' => 38,
            'status'              => 'available',
            'verification_status' => 'rejected',
            'verification_notes'  => 'Registration documents are not clear. Please re-upload the original registration card.',
        ]);

        $m7 = Motorcycle::create([
            'owner_id'            => $owner2->id,
            'driver_id'           => null,
            'plate_number'        => 'T 901 JKL',
            'make'                => 'TVS',
            'model'               => 'XL 100',
            'year'                => 2023,
            'color'               => 'Green',
            'engine_cc'           => 100,
            'engine_number'       => 'EN-TVS-2023-901J',
            'chassis_number'      => 'CH-TVS-2023-901J',
            'weekly_amount'       => 40000,
            'loan_amount'         => 1000000,
            'loan_duration_weeks' => 25,
            'status'              => 'available',
            'verification_status' => 'verified',
        ]);

        $m8 = Motorcycle::create([
            'owner_id'            => $owner2->id,
            'driver_id'           => $driver2->id,
            'plate_number'        => 'T 205 GHI',
            'make'                => 'Honda',
            'model'               => 'Dio',
            'year'                => 2024,
            'color'               => 'Blue',
            'engine_cc'           => 110,
            'engine_number'       => 'EN-HONDA-2024-205G',
            'chassis_number'      => 'CH-HONDA-2024-205G',
            'weekly_amount'       => 50000,
            'loan_amount'         => 1500000,
            'loan_duration_weeks' => 30,
            'status'              => 'assigned',
            'verification_status' => 'verified',
        ]);

        // ──────────────────────────────────────────────
        //  LOANS  (4 total)
        // ──────────────────────────────────────────────

        // Loan 1 — driver1, active, 60% paid (8 of 20 weeks)
        $loan1 = Loan::create([
            'motorcycle_id'        => $m3->id,
            'owner_id'             => $owner1->id,
            'driver_id'            => $driver1->id,
            'total_amount'         => 1800000,
            'weekly_installment'   => 55000,
            'amount_paid'          => 880000,
            'duration_weeks'       => 32,
            'start_date'           => now()->subWeeks(10),
            'end_date'             => now()->addWeeks(22),
            'next_payment_date'    => now()->addDays(4),
            'status'               => 'active',
            'agreement_accepted_at'=> now()->subWeeks(10),
        ]);

        // Loan 2 — driver2, overdue, 30% paid
        $loan2 = Loan::create([
            'motorcycle_id'        => $m4->id,
            'owner_id'             => $owner1->id,
            'driver_id'            => $driver2->id,
            'total_amount'         => 1200000,
            'weekly_installment'   => 45000,
            'amount_paid'          => 360000,
            'duration_weeks'       => 26,
            'start_date'           => now()->subWeeks(14),
            'end_date'             => now()->addWeeks(12),
            'next_payment_date'    => now()->subDays(14),
            'status'               => 'overdue',
            'agreement_accepted_at'=> now()->subWeeks(14),
        ]);

        // Loan 3 — driver2, active, 90% paid (almost done)
        $loan3 = Loan::create([
            'motorcycle_id'        => $m8->id,
            'owner_id'             => $owner2->id,
            'driver_id'            => $driver2->id,
            'total_amount'         => 1500000,
            'weekly_installment'   => 50000,
            'amount_paid'          => 1350000,
            'duration_weeks'       => 30,
            'start_date'           => now()->subWeeks(27),
            'end_date'             => now()->addWeeks(3),
            'next_payment_date'    => now()->addDays(2),
            'status'               => 'active',
            'agreement_accepted_at'=> now()->subWeeks(27),
        ]);

        // Loan 4 — completed, 100% paid
        $loan4 = Loan::create([
            'motorcycle_id'             => $m5->id,
            'owner_id'                  => $owner1->id,
            'driver_id'                 => $driver1->id,
            'total_amount'              => 800000,
            'weekly_installment'        => 40000,
            'amount_paid'               => 800000,
            'duration_weeks'            => 20,
            'start_date'                => now()->subWeeks(22),
            'end_date'                  => now()->subWeeks(2),
            'next_payment_date'         => null,
            'status'                    => 'completed',
            'agreement_accepted_at'     => now()->subWeeks(22),
            'ownership_certificate_generated' => true,
        ]);

        // ──────────────────────────────────────────────
        //  PAYMENTS  (18 total across 4 loans)
        // ──────────────────────────────────────────────

        // Loan 1 payments — 8 verified, 1 pending
        for ($i = 0; $i < 8; $i++) {
            Payment::create([
                'loan_id'       => $loan1->id,
                'amount'        => 55000,
                'payment_date'  => now()->subWeeks(10 - $i),
                'method'        => ['cash', 'mpesa', 'bank'][array_rand(['cash', 'mpesa', 'bank'])],
                'status'        => 'verified',
                'reference_number' => 'MP-' . strtoupper(bin2hex(random_bytes(3))) . ($i + 1),
            ]);
        }
        Payment::create([
            'loan_id'       => $loan1->id,
            'amount'        => 55000,
            'payment_date'  => now()->subDays(1),
            'method'        => 'mpesa',
            'status'        => 'pending_verification',
            'reference_number' => 'MP-' . strtoupper(bin2hex(random_bytes(4))),
            'notes'         => 'Paid via M-Pesa till number 12345',
        ]);

        // Loan 2 payments — 5 verified, 1 rejected
        for ($i = 0; $i < 5; $i++) {
            Payment::create([
                'loan_id'       => $loan2->id,
                'amount'        => 45000,
                'payment_date'  => now()->subWeeks(14 - $i),
                'method'        => ['cash', 'mpesa', 'bank'][array_rand(['cash', 'mpesa', 'bank'])],
                'status'        => 'verified',
                'reference_number' => 'MP-' . strtoupper(bin2hex(random_bytes(3))) . ($i + 10),
            ]);
        }
        Payment::create([
            'loan_id'         => $loan2->id,
            'amount'          => 45000,
            'payment_date'    => now()->subWeeks(6),
            'method'          => 'mpesa',
            'status'          => 'rejected',
            'reference_number'=> 'MP-' . strtoupper(bin2hex(random_bytes(4))),
            'rejection_reason'=> 'Amount does not match the weekly installment. Expected TSh 45,000.',
        ]);

        // Loan 3 payments — 9 verified, 2 pending, 1 rejected
        for ($i = 0; $i < 9; $i++) {
            Payment::create([
                'loan_id'       => $loan3->id,
                'amount'        => 50000,
                'payment_date'  => now()->subWeeks(27 - $i),
                'method'        => ['cash', 'mpesa', 'bank'][array_rand(['cash', 'mpesa', 'bank'])],
                'status'        => 'verified',
                'reference_number' => 'MP-' . strtoupper(bin2hex(random_bytes(3))) . ($i + 20),
            ]);
        }
        Payment::create([
            'loan_id'       => $loan3->id,
            'amount'        => 50000,
            'payment_date'  => now()->subWeeks(1),
            'method'        => 'bank',
            'status'        => 'pending_verification',
            'reference_number' => 'BNK-' . strtoupper(bin2hex(random_bytes(4))),
            'notes'         => 'Bank transfer to NMB account ending 5678',
        ]);
        Payment::create([
            'loan_id'         => $loan3->id,
            'amount'          => 50000,
            'payment_date'    => now()->subWeeks(3),
            'method'          => 'cash',
            'status'          => 'rejected',
            'reference_number'=> null,
            'rejection_reason'=> 'Cash payment not confirmed by owner.',
        ]);

        // ──────────────────────────────────────────────
        //  APPLICATIONS  (3 total)
        // ──────────────────────────────────────────────

        Application::create([
            'motorcycle_id'   => $m2->id,
            'driver_id'       => $driver3->id,
            'status'          => 'pending',
            'notes'           => 'Nina uzoefu wa miaka 3 ya kuendesha bodaboda. Nina leseni na niko tayari kuanza mara moja.',
            'id_number'       => '19960412123456',
            'license_number'  => 'DL-TZ-2024-0078',
            'guarantor_name'  => 'Hamisi Juma',
            'guarantor_phone' => '0765432100',
        ]);

        Application::create([
            'motorcycle_id'   => $m7->id,
            'driver_id'       => $driver1->id,
            'status'          => 'approved',
            'notes'           => 'Ninapenda kuongeza bodaboda nyingine.',
            'admin_notes'     => 'Driver has good payment history on existing loan.',
            'id_number'       => '19951110123456',
            'license_number'  => 'DL-TZ-2023-0042',
        ]);

        Application::create([
            'motorcycle_id'   => $m2->id,
            'driver_id'       => $driver2->id,
            'status'          => 'rejected',
            'notes'           => 'Ninahitaji bodaboda mpya kwa ajili ya kazi.',
            'admin_notes'     => 'Currently has two active loans. Cannot approve additional loan.',
            'id_number'       => '19980228123456',
            'license_number'  => 'DL-TZ-2023-0055',
        ]);

        // ──────────────────────────────────────────────
        //  NOTIFICATIONS
        // ──────────────────────────────────────────────

        UserNotification::createNotification(
            $admin->id,
            'application_submitted',
            'New Application',
            'Peter Shirima has applied for Bajaj Boxer BM150 (T 781 MNO).',
            ['application_id' => 1, 'motorcycle_id' => $m2->id]
        );
        UserNotification::createNotification(
            $admin->id,
            'payment_submitted',
            'Payment Pending Review',
            'Joseph Massawe submitted a M-Pesa payment of TSh 55,000 for Loan #' . $loan1->id . '.',
            ['loan_id' => $loan1->id, 'payment_id' => $loan1->payments->last()->id ?? null]
        );

        UserNotification::createNotification(
            $owner1->id,
            'motorcycle_verified',
            'Vehicle Verified',
            'Your Bajaj Boxer BM150 (T 781 MNO) has been verified and is now available for hire.',
            ['motorcycle_id' => $m2->id]
        );
        UserNotification::createNotification(
            $owner1->id,
            'payment_verified',
            'Payment Received',
            'A weekly payment of TSh 55,000 has been verified for your Honda Livo (T 119 BDF).',
            ['motorcycle_id' => $m3->id, 'loan_id' => $loan1->id]
        );

        UserNotification::createNotification(
            $owner2->id,
            'motorcycle_rejected',
            'Vehicle Rejected',
            'Your Bajaj Pulsar 150 (T 882 CZK) was rejected. Reason: Registration documents are not clear.',
            ['motorcycle_id' => $m6->id]
        );

        UserNotification::createNotification(
            $driver1->id,
            'loan_created',
            'New Loan Active',
            'Your loan for Honda Livo (T 119 BDF) is now active. Weekly installment: TSh 55,000.',
            ['loan_id' => $loan1->id, 'motorcycle_id' => $m3->id]
        );
        UserNotification::createNotification(
            $driver1->id,
            'payment_verified',
            'Payment Confirmed',
            'Your payment of TSh 55,000 has been confirmed. Thank you!',
            ['loan_id' => $loan1->id]
        );
        UserNotification::createNotification(
            $driver1->id,
            'loan_completed',
            'Loan Completed',
            'Congratulations! Your loan for TVS Apache RTR (T 567 XYZ) is fully paid. Ownership certificate generated.',
            ['loan_id' => $loan4->id, 'motorcycle_id' => $m5->id],
            now()->subWeeks(2)
        );

        UserNotification::createNotification(
            $driver2->id,
            'overdue_payment',
            'Payment Overdue',
            'Your weekly payment for Yamaha Fascino (T 334 PQR) is 2 weeks overdue. Please pay TSh 45,000 immediately.',
            ['loan_id' => $loan2->id, 'motorcycle_id' => $m4->id]
        );
        UserNotification::createNotification(
            $driver2->id,
            'payment_rejected',
            'Payment Rejected',
            'Your payment of TSh 45,000 was rejected. Reason: Amount does not match the weekly installment.',
            ['loan_id' => $loan2->id]
        );
        UserNotification::createNotification(
            $driver2->id,
            'motorcycle_assigned',
            'Vehicle Assigned',
            'You have been assigned Honda Dio (T 205 GHI).',
            ['motorcycle_id' => $m8->id, 'loan_id' => $loan3->id]
        );
    }
}

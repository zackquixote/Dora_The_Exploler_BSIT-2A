<?php

namespace App\Services;

use App\Models\LogModel;
use App\Models\NotificationModel;
use App\Models\ResidentAccountModel;
use App\Models\ResidentModel;
use App\Models\ResidentVerificationFileModel;
use App\Models\ResidentVerificationModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\I18n\Time;
use Config\Database;
use RuntimeException;

class ResidentVerificationService
{
    private ResidentAccountModel $accountModel;
    private ResidentModel $residentModel;
    private ResidentVerificationModel $verificationModel;
    private ResidentVerificationFileModel $fileModel;
    private NotificationService $notificationService;
    private NotificationModel $notificationModel;
    private LogModel $logModel;

    public function __construct()
    {
        $this->accountModel = new ResidentAccountModel();
        $this->residentModel = new ResidentModel();
        $this->verificationModel = new ResidentVerificationModel();
        $this->fileModel = new ResidentVerificationFileModel();
        $this->notificationService = new NotificationService();
        $this->notificationModel = new NotificationModel();
        $this->logModel = new LogModel();
    }

    public function register(array $data, array $files): array
    {
        $db = Database::connect();
        $db->transStart();

        $matchedResident = $this->findResidentMatch($data);
        $residentId = $matchedResident['id'] ?? null;

        $accountId = (int) $this->accountModel->insert([
            'resident_id'   => $residentId,
            'email'         => $data['email'] ?: null,
            'phone'         => $data['phone'] ?: null,
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'status'        => 'pending_verification',
        ], true);

        $verificationId = (int) $this->verificationModel->insert([
            'resident_account_id'      => $accountId,
            'resident_id'              => $residentId,
            'first_name'               => $data['first_name'],
            'middle_name'              => $data['middle_name'] ?: null,
            'last_name'                => $data['last_name'],
            'birthdate'                => $data['birthdate'] ?: null,
            'address_submitted'        => $data['address'],
            'contact_email_submitted'  => $data['email'] ?: null,
            'contact_phone_submitted'  => $data['phone'] ?: null,
            'national_id_number'       => $data['national_id_number'],
            'status'                   => 'pending_admin_review',
            'otp_required'             => $this->shouldRequireOtp() ? 1 : 0,
            'otp_channel'              => $this->normalizeOtpChannel($data['otp_channel'], $data['email'], $data['phone']),
            'submitted_at'             => Time::now()->toDateTimeString(),
        ], true);

        $storedFiles = $this->storeFiles($verificationId, $files);

        $db->transComplete();

        if (! $db->transStatus()) {
            throw new RuntimeException('Unable to submit verification request.');
        }

        $this->notifyResidentSubmission($data, $residentId);
        $this->notifyAdminsOfPendingVerification($verificationId, $data);

        return [
            'account_id'      => $accountId,
            'verification_id' => $verificationId,
            'resident_id'     => $residentId,
            'files'           => $storedFiles,
        ];
    }

    public function resubmit(int $accountId, array $data, array $files): array
    {
        $verification = $this->verificationModel->getLatestForAccount($accountId);
        if (! $verification) {
            throw new RuntimeException('Verification request not found.');
        }

        $residentId = $verification['resident_id'] ?: ($this->findResidentMatch($data)['id'] ?? null);

        $this->verificationModel->update($verification['id'], [
            'resident_id'             => $residentId,
            'first_name'              => $data['first_name'],
            'middle_name'             => $data['middle_name'] ?: null,
            'last_name'               => $data['last_name'],
            'birthdate'               => $data['birthdate'] ?: null,
            'address_submitted'       => $data['address'],
            'contact_email_submitted' => $data['email'] ?: null,
            'contact_phone_submitted' => $data['phone'] ?: null,
            'national_id_number'      => $data['national_id_number'],
            'status'                  => 'pending_admin_review',
            'rejection_reason'        => null,
            'resubmission_reason'     => null,
            'review_notes'            => null,
            'reviewed_by'             => null,
            'reviewed_at'             => null,
            'submitted_at'            => Time::now()->toDateTimeString(),
        ]);

        $this->accountModel->update($accountId, [
            'resident_id' => $residentId,
            'email'       => $data['email'] ?: null,
            'phone'       => $data['phone'] ?: null,
            'status'      => 'pending_verification',
        ]);

        $storedFiles = $this->storeFiles((int) $verification['id'], $files);
        $this->notifyAdminsOfPendingVerification((int) $verification['id'], $data, true);

        return [
            'verification_id' => (int) $verification['id'],
            'resident_id'     => $residentId,
            'files'           => $storedFiles,
        ];
    }

    public function approve(int $verificationId, int $reviewerId, int $residentId, bool $otpRequired, ?string $otpChannel, ?string $reviewNotes = null): array
    {
        $verification = $this->verificationModel->find($verificationId);
        if (! $verification) {
            throw new RuntimeException('Verification record not found.');
        }

        $resident = $this->residentModel->find($residentId);
        if (! $resident) {
            throw new RuntimeException('Selected resident record does not exist.');
        }

        $accountId = (int) $verification['resident_account_id'];
        $accountStatus = 'active';
        $verificationStatus = 'verified';
        $otpCode = null;
        $otpExpiry = null;
        $normalizedChannel = null;

        if ($otpRequired) {
            $normalizedChannel = $this->normalizeOtpChannel($otpChannel, $verification['contact_email_submitted'], $verification['contact_phone_submitted']);
            if (! $normalizedChannel) {
                throw new RuntimeException('A valid OTP channel is required when OTP verification is enabled.');
            }

            $otpCode = $this->generateOtpCode();
            $otpExpiry = Time::now()->addMinutes(10)->toDateTimeString();
            $accountStatus = 'pending_otp';
            $verificationStatus = 'pending_otp';
        }

        $this->verificationModel->update($verificationId, [
            'resident_id'        => $residentId,
            'status'             => $verificationStatus,
            'otp_required'       => $otpRequired ? 1 : 0,
            'otp_channel'        => $normalizedChannel,
            'otp_code_hash'      => $otpCode ? password_hash($otpCode, PASSWORD_DEFAULT) : null,
            'otp_expires_at'     => $otpExpiry,
            'otp_sent_at'        => $otpCode ? Time::now()->toDateTimeString() : null,
            'otp_verified_at'    => null,
            'otp_attempt_count'  => 0,
            'review_notes'       => $reviewNotes ?: null,
            'rejection_reason'   => null,
            'resubmission_reason'=> null,
            'reviewed_by'        => $reviewerId,
            'reviewed_at'        => Time::now()->toDateTimeString(),
        ]);

        $this->accountModel->update($accountId, [
            'resident_id'       => $residentId,
            'status'            => $accountStatus,
            'rejection_reason'  => null,
            'verified_at'       => ! $otpRequired ? Time::now()->toDateTimeString() : null,
        ]);

        $this->logModel->addLog(
            'Reviewed resident verification #' . $verificationId . ' and marked it as ' . $verificationStatus,
            'resident_verification'
        );

        $this->notifyResidentApproved($verification, $otpRequired);

        if ($otpCode) {
            $this->sendOtp($verification, $otpCode, $normalizedChannel);
        }

        return [
            'account_status'      => $accountStatus,
            'verification_status' => $verificationStatus,
        ];
    }

    public function reject(int $verificationId, int $reviewerId, string $reason): void
    {
        $verification = $this->verificationModel->find($verificationId);
        if (! $verification) {
            throw new RuntimeException('Verification record not found.');
        }

        $this->verificationModel->update($verificationId, [
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by'      => $reviewerId,
            'reviewed_at'      => Time::now()->toDateTimeString(),
        ]);

        $this->accountModel->update((int) $verification['resident_account_id'], [
            'status'           => 'rejected',
            'rejection_reason' => $reason,
        ]);

        $this->logModel->addLog(
            'Rejected resident verification #' . $verificationId . ' - ' . $reason,
            'resident_verification'
        );

        $this->notifyResidentRejected($verification, $reason);
    }

    public function requestResubmission(int $verificationId, int $reviewerId, string $reason): void
    {
        $verification = $this->verificationModel->find($verificationId);
        if (! $verification) {
            throw new RuntimeException('Verification record not found.');
        }

        $this->verificationModel->update($verificationId, [
            'status'              => 'needs_resubmission',
            'resubmission_reason' => $reason,
            'reviewed_by'         => $reviewerId,
            'reviewed_at'         => Time::now()->toDateTimeString(),
        ]);

        $this->accountModel->update((int) $verification['resident_account_id'], [
            'status' => 'needs_resubmission',
        ]);

        $this->logModel->addLog(
            'Requested new ID documents for verification #' . $verificationId,
            'resident_verification'
        );

        $this->notifyResidentResubmissionRequested($verification, $reason);
    }

    public function verifyOtp(int $accountId, string $otpCode): bool
    {
        $verification = $this->verificationModel->getLatestForAccount($accountId);
        if (! $verification || $verification['status'] !== 'pending_otp') {
            return false;
        }

        if (empty($verification['otp_code_hash']) || empty($verification['otp_expires_at'])) {
            return false;
        }

        if (strtotime((string) $verification['otp_expires_at']) < time()) {
            return false;
        }

        $attempts = (int) ($verification['otp_attempt_count'] ?? 0);
        if ($attempts >= 5) {
            return false;
        }

        $this->verificationModel->update((int) $verification['id'], [
            'otp_attempt_count' => $attempts + 1,
        ]);

        if (! password_verify($otpCode, (string) $verification['otp_code_hash'])) {
            return false;
        }

        $now = Time::now()->toDateTimeString();

        $this->verificationModel->update((int) $verification['id'], [
            'status'            => 'verified',
            'otp_code_hash'     => null,
            'otp_expires_at'    => null,
            'otp_verified_at'   => $now,
            'reviewed_at'       => $verification['reviewed_at'] ?? $now,
        ]);

        $this->accountModel->update($accountId, [
            'status'      => 'active',
            'verified_at' => $now,
        ]);

        return true;
    }

    public function resendOtp(int $accountId): bool
    {
        $verification = $this->verificationModel->getLatestForAccount($accountId);
        if (! $verification || $verification['status'] !== 'pending_otp') {
            return false;
        }

        if (! empty($verification['otp_sent_at']) && strtotime((string) $verification['otp_sent_at']) > (time() - 60)) {
            return false;
        }

        $channel = $verification['otp_channel'] ?: $this->normalizeOtpChannel(
            null,
            $verification['contact_email_submitted'],
            $verification['contact_phone_submitted']
        );

        if (! $channel) {
            return false;
        }

        $otpCode = $this->generateOtpCode();

        $this->verificationModel->update((int) $verification['id'], [
            'otp_channel'       => $channel,
            'otp_code_hash'     => password_hash($otpCode, PASSWORD_DEFAULT),
            'otp_expires_at'    => Time::now()->addMinutes(10)->toDateTimeString(),
            'otp_sent_at'       => Time::now()->toDateTimeString(),
            'otp_attempt_count' => 0,
        ]);

        $this->sendOtp($verification, $otpCode, $channel);

        return true;
    }

    public function getLatestVerificationForAccount(int $accountId): ?array
    {
        return $this->verificationModel->getLatestForAccount($accountId);
    }

    public function getVerificationFiles(int $verificationId): array
    {
        return $this->fileModel->getForVerification($verificationId);
    }

    public function getVerificationFile(int $fileId): ?array
    {
        return $this->fileModel->find($fileId);
    }

    public function getProtectedFilePath(array $file): string
    {
        $path = WRITEPATH . ltrim((string) $file['storage_path'], '\\/');
        if (! is_file($path)) {
            throw new RuntimeException('Verification file not found.');
        }

        return $path;
    }

    private function findResidentMatch(array $data): ?array
    {
        return $this->residentModel
            ->where('first_name', $data['first_name'])
            ->where('last_name', $data['last_name'])
            ->where('birthdate', $data['birthdate'])
            ->where('deleted_at', null)
            ->first();
    }

    private function shouldRequireOtp(): bool
    {
        return filter_var((string) env('RESIDENT_VERIFICATION_REQUIRE_OTP', '0'), FILTER_VALIDATE_BOOL);
    }

    private function normalizeOtpChannel(?string $requestedChannel, ?string $email, ?string $phone): ?string
    {
        $requestedChannel = strtolower(trim((string) $requestedChannel));

        if ($requestedChannel === 'sms' && ! empty($phone)) {
            return 'sms';
        }

        if ($requestedChannel === 'email' && ! empty($email)) {
            return 'email';
        }

        if (! empty($phone)) {
            return 'sms';
        }

        if (! empty($email)) {
            return 'email';
        }

        return null;
    }

    private function storeFiles(int $verificationId, array $files): array
    {
        $storedFiles = [];

        $fileMap = [
            'national_id_front' => $files['national_id_front'] ?? null,
            'national_id_back'  => $files['national_id_back'] ?? null,
            'supporting_document' => $files['supporting_document'] ?? null,
        ];

        foreach ($fileMap as $type => $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid() || $file->hasMoved()) {
                continue;
            }

            $relativeDir = 'uploads/resident_verifications/' . $verificationId . '/';
            $targetDir = WRITEPATH . $relativeDir;
            if (! is_dir($targetDir)) {
                mkdir($targetDir, 0775, true);
            }

            $newName = $type . '_' . $file->getRandomName();
            $file->move($targetDir, $newName);

            $fileId = (int) $this->fileModel->insert([
                'resident_verification_id' => $verificationId,
                'file_type'                => $type,
                'storage_path'             => $relativeDir . $newName,
                'original_name'            => $file->getClientName(),
                'mime_type'                => $file->getClientMimeType(),
                'file_size'                => $file->getSize(),
                'is_primary'               => $type === 'national_id_front' ? 1 : 0,
                'uploaded_at'              => Time::now()->toDateTimeString(),
                'created_at'               => Time::now()->toDateTimeString(),
            ], true);

            $storedFiles[] = array_merge(
                ['id' => $fileId],
                $this->fileModel->find($fileId) ?? []
            );
        }

        return $storedFiles;
    }

    private function generateOtpCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function sendOtp(array $verification, string $otpCode, string $channel): void
    {
        $message = 'Your Barangay Portal verification code is ' . $otpCode . '. It expires in 10 minutes.';
        $title = 'Barangay Portal OTP';

        if ($channel === 'email' && ! empty($verification['contact_email_submitted'])) {
            $this->notificationService->sendDirectEmail((string) $verification['contact_email_submitted'], $title, $message);
            return;
        }

        if ($channel === 'sms' && ! empty($verification['contact_phone_submitted'])) {
            $this->notificationService->sendDirectSms((string) $verification['contact_phone_submitted'], $message);
        }
    }

    private function notifyResidentSubmission(array $data, ?int $residentId): void
    {
        $message = 'Your ID verification request was received and is now pending admin review.';

        if ($residentId) {
            try {
                $this->notificationModel->insert([
                    'recipient_type' => 'resident',
                    'recipient_id'   => $residentId,
                    'type'           => 'resident_verification_received',
                    'title'          => 'Verification Request Received',
                    'message'        => $message,
                    'channels'       => json_encode(['in_app']),
                    'status'         => 'sent',
                    'sent_at'        => Time::now()->toDateTimeString(),
                    'metadata'       => json_encode(['module' => 'resident_verification']),
                ]);
            } catch (\Throwable $e) {
            }
        }

        if (! empty($data['email'])) {
            $this->notificationService->sendDirectEmail((string) $data['email'], 'Verification Request Received', $message);
        }

        if (! empty($data['phone'])) {
            $this->notificationService->sendDirectSms((string) $data['phone'], $message);
        }
    }

    private function notifyResidentApproved(array $verification, bool $otpRequired): void
    {
        $message = $otpRequired
            ? 'Your ID has been approved. Please complete OTP verification to activate your portal account.'
            : 'Your ID has been approved and your portal account is now active.';

        if (! empty($verification['contact_email_submitted'])) {
            $this->notificationService->sendDirectEmail((string) $verification['contact_email_submitted'], 'Verification Approved', $message);
        }

        if (! empty($verification['contact_phone_submitted'])) {
            $this->notificationService->sendDirectSms((string) $verification['contact_phone_submitted'], $message);
        }
    }

    private function notifyResidentRejected(array $verification, string $reason): void
    {
        $message = 'Your ID verification was rejected. Reason: ' . $reason;

        if (! empty($verification['contact_email_submitted'])) {
            $this->notificationService->sendDirectEmail((string) $verification['contact_email_submitted'], 'Verification Rejected', $message);
        }

        if (! empty($verification['contact_phone_submitted'])) {
            $this->notificationService->sendDirectSms((string) $verification['contact_phone_submitted'], $message);
        }
    }

    private function notifyResidentResubmissionRequested(array $verification, string $reason): void
    {
        $message = 'Please upload a new ID image or supporting document. Reason: ' . $reason;

        if (! empty($verification['contact_email_submitted'])) {
            $this->notificationService->sendDirectEmail((string) $verification['contact_email_submitted'], 'New Documents Requested', $message);
        }

        if (! empty($verification['contact_phone_submitted'])) {
            $this->notificationService->sendDirectSms((string) $verification['contact_phone_submitted'], $message);
        }
    }

    private function notifyAdminsOfPendingVerification(int $verificationId, array $data, bool $resubmission = false): void
    {
        try {
            $this->notificationModel->insert([
                'recipient_type' => 'group',
                'recipient_id'   => null,
                'type'           => 'resident_verification_pending',
                'title'          => $resubmission ? 'Resident Verification Resubmitted' : 'New Resident Verification Pending',
                'message'        => trim($data['first_name'] . ' ' . $data['last_name']) . ' submitted documents for admin review.',
                'channels'       => json_encode(['in_app']),
                'status'         => 'sent',
                'sent_at'        => Time::now()->toDateTimeString(),
                'metadata'       => json_encode([
                    'module'          => 'resident_verification',
                    'verification_id' => $verificationId,
                ]),
            ]);
        } catch (\Throwable $e) {
        }
    }
}

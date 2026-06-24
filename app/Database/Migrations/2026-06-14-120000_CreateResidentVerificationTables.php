<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResidentVerificationTables extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('resident_accounts', [
            'resident_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'pending_verification',
            ],
        ]);

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'resident_account_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'resident_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'middle_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'birthdate' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'address_submitted' => [
                'type' => 'TEXT',
            ],
            'contact_email_submitted' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'contact_phone_submitted' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'national_id_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'pending_admin_review',
            ],
            'otp_required' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'otp_channel' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'otp_code_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'otp_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'otp_sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'otp_verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'otp_attempt_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'review_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'rejection_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'resubmission_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'reviewed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'reviewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('resident_account_id');
        $this->forge->addKey('resident_id');
        $this->forge->addKey('status');
        $this->forge->addKey('reviewed_by');
        $this->forge->createTable('resident_verifications', true);

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'resident_verification_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'file_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'storage_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'original_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'mime_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
            ],
            'file_size' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'is_primary' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'uploaded_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('resident_verification_id');
        $this->forge->addKey('file_type');
        $this->forge->createTable('resident_verification_files', true);
    }

    public function down()
    {
        $this->forge->dropTable('resident_verification_files', true);
        $this->forge->dropTable('resident_verifications', true);

        $this->forge->modifyColumn('resident_accounts', [
            'resident_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'active', 'disabled'],
                'default'    => 'pending',
            ],
        ]);
    }
}

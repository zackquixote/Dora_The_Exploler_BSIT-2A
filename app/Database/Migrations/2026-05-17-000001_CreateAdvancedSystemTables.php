<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Advanced System Tables Migration
 * Creates foundational tables for all new features
 */
class CreateAdvancedSystemTables extends Migration
{
    public function up()
    {
        // Notifications System
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'recipient_type' => [
                'type' => 'ENUM',
                'constraint' => ['resident', 'user', 'all', 'group'],
                'default' => 'resident',
            ],
            'recipient_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'channels' => [
                'type' => 'JSON',
                'comment' => 'SMS, Email, Push, In-App',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'sent', 'delivered', 'failed', 'read'],
                'default' => 'pending',
            ],
            'scheduled_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'metadata' => [
                'type' => 'JSON',
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
        $this->forge->addKey(['recipient_type', 'recipient_id']);
        $this->forge->addKey('status');
        $this->forge->createTable('notifications', true);

        // Document Management System
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'entity_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'resident, blotter, certificate, etc.',
            ],
            'entity_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'document_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'file_size' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'mime_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'file_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'comment' => 'SHA256 hash for integrity',
            ],
            'version' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'access_level' => [
                'type' => 'ENUM',
                'constraint' => ['public', 'internal', 'restricted', 'confidential'],
                'default' => 'internal',
            ],
            'uploaded_by' => [
                'type' => 'INT',
                'constraint' => 11,
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
        $this->forge->addKey(['entity_type', 'entity_id']);
        $this->forge->addKey('file_hash');
        $this->forge->createTable('documents', true);

        // Health Information System
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'resident_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'blood_type' => [
                'type' => 'ENUM',
                'constraint' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Unknown'],
                'default' => 'Unknown',
            ],
            'allergies' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'medical_conditions' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'emergency_contact_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'emergency_contact_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'emergency_contact_relationship' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'insurance_provider' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'insurance_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'vaccination_records' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'last_checkup_date' => [
                'type' => 'DATE',
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
        $this->forge->addKey('resident_id');
        $this->forge->createTable('health_records', true);

        // Business Registration System
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'business_permit_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'owner_resident_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'business_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'business_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'business_address' => [
                'type' => 'TEXT',
            ],
            'contact_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'capital_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'employees_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'permit_fee' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'expired', 'suspended', 'cancelled'],
                'default' => 'active',
            ],
            'issue_date' => [
                'type' => 'DATE',
            ],
            'expiry_date' => [
                'type' => 'DATE',
            ],
            'renewal_reminder_sent' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
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
        $this->forge->addKey('business_permit_number');
        $this->forge->addKey('owner_resident_id');
        $this->forge->addKey('status');
        $this->forge->createTable('business_permits', true);

        // Payment System
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'receipt_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'payer_type' => [
                'type' => 'ENUM',
                'constraint' => ['resident', 'business', 'external'],
                'default' => 'resident',
            ],
            'payer_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'service_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'service_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'payment_method' => [
                'type' => 'ENUM',
                'constraint' => ['cash', 'gcash', 'bank_transfer', 'check', 'online'],
                'default' => 'cash',
            ],
            'reference_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'completed', 'cancelled', 'refunded'],
                'default' => 'completed',
            ],
            'collected_by' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'notes' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('receipt_number');
        $this->forge->addKey(['payer_type', 'payer_id']);
        $this->forge->addKey('service_type');
        $this->forge->createTable('payments', true);

        // Events Management System
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'event_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'event_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'venue' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'start_date' => [
                'type' => 'DATETIME',
            ],
            'end_date' => [
                'type' => 'DATETIME',
            ],
            'max_participants' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'registration_required' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'registration_deadline' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'target_audience' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Age groups, gender, etc.',
            ],
            'organizer_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'budget' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['planning', 'open', 'ongoing', 'completed', 'cancelled'],
                'default' => 'planning',
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
        $this->forge->addKey('event_code');
        $this->forge->addKey('status');
        $this->forge->addKey('start_date');
        $this->forge->createTable('events', true);

        // Event Participants
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'event_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'resident_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'registration_date' => [
                'type' => 'DATETIME',
            ],
            'attendance_status' => [
                'type' => 'ENUM',
                'constraint' => ['registered', 'attended', 'absent', 'cancelled'],
                'default' => 'registered',
            ],
            'check_in_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'check_out_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'feedback_rating' => [
                'type' => 'INT',
                'constraint' => 1,
                'null' => true,
                'comment' => '1-5 rating',
            ],
            'feedback_comment' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['event_id', 'resident_id']);
        $this->forge->addKey('attendance_status');
        $this->forge->createTable('event_participants', true);

        // Emergency Response System
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'incident_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'emergency_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'severity_level' => [
                'type' => 'ENUM',
                'constraint' => ['low', 'medium', 'high', 'critical'],
                'default' => 'medium',
            ],
            'location' => [
                'type' => 'TEXT',
            ],
            'coordinates' => [
                'type' => 'POINT',
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'reporter_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'reporter_contact' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'affected_residents' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'response_team' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['reported', 'dispatched', 'responding', 'resolved', 'closed'],
                'default' => 'reported',
            ],
            'response_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'resolution_time' => [
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
        $this->forge->addKey('incident_number');
        $this->forge->addKey('emergency_type');
        $this->forge->addKey('status');
        $this->forge->createTable('emergency_incidents', true);

        // Audit Logs (Enhanced)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'entity' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'entity_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'old_data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'new_data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'session_id' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey(['entity', 'entity_id']);
        $this->forge->addKey('action');
        $this->forge->addKey('created_at');
        $this->forge->createTable('audit_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
        $this->forge->dropTable('documents');
        $this->forge->dropTable('health_records');
        $this->forge->dropTable('business_permits');
        $this->forge->dropTable('payments');
        $this->forge->dropTable('events');
        $this->forge->dropTable('event_participants');
        $this->forge->dropTable('emergency_incidents');
        $this->forge->dropTable('audit_logs');
    }
}
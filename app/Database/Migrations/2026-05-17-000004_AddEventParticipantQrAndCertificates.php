<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Phase 1.3 - Events QR check-in support
 * Adds QR token + certificate metadata to event_participants.
 */
class AddEventParticipantQrAndCertificates extends Migration
{
    public function up()
    {
        // Add columns only if table exists (defensive)
        $db = \Config\Database::connect();
        if (! $db->tableExists('event_participants')) {
            return;
        }

        $fields = [
            'qr_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'resident_id',
            ],
            'qr_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'qr_token',
            ],
            'checked_in_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'check_in_time',
            ],
            'certificate_document_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'feedback_comment',
            ],
            'certificate_generated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'certificate_document_id',
            ],
        ];

        // Only add if not present
        $existing = array_map('strtolower', $db->getFieldNames('event_participants'));
        $toAdd = [];
        foreach ($fields as $name => $def) {
            if (! in_array(strtolower($name), $existing, true)) {
                $toAdd[$name] = $def;
            }
        }

        if ($toAdd !== []) {
            $this->forge->addColumn('event_participants', $toAdd);
        }

        // Add unique index for QR token (ALTER TABLE via raw SQL for compatibility)
        try {
            $db->query('CREATE UNIQUE INDEX idx_event_participants_qr_token ON event_participants(qr_token)');
        } catch (\Throwable $e) {
            // ignore if already exists or unsupported
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('event_participants')) {
            return;
        }

        $columns = $db->getFieldNames('event_participants');
        $drop = [];
        foreach (['qr_token', 'qr_expires_at', 'checked_in_by', 'certificate_document_id', 'certificate_generated_at'] as $col) {
            if (in_array($col, $columns, true)) {
                $drop[] = $col;
            }
        }

        if ($drop !== []) {
            $this->forge->dropColumn('event_participants', $drop);
        }

        try {
            $db->query('DROP INDEX idx_event_participants_qr_token ON event_participants');
        } catch (\Throwable $e) {
            // ignore
        }
    }
}

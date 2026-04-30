<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * CertificateTypeModel
 * 
 * Stores customizable certificate templates (HTML/content) for different certificate types.
 * 
 * TABLE: certificate_types
 * - Acts as a template repository; each record defines the name and content
 *   (e.g., letter body) for a specific certificate type.
 * 
 * FIELDS:
 * - name: Display name of certificate (matches CertificateModel.certificate_type)
 * - content: The HTML/text template for the certificate
 * - created_by: ID of the user who created/updated the template
 * 
 * TIMESTAMPS: created_at, updated_at
 * 
 * VALIDATION:
 * - name: required, min length 3, max 150
 * - content: optional, max length 5000
 * 
 * @package App\Models
 */
class CertificateTypeModel extends Model
{
    protected $table            = 'certificate_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'name',
        'content',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'    => 'required|min_length[3]|max_length[150]',
        'content' => 'permit_empty|string|max_length[5000]',
    ];
}
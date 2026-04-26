<?php

class EnrollmentDocument extends Model
{
    /**
     * Get document by ID
     */
    public function getById($documentId)
    {
        $sql = "SELECT ed.*, e.parent_id, e.learner_first_name, e.learner_last_name
                FROM enrollment_documents ed
                JOIN enrollments e ON ed.enrollment_id = e.id
                WHERE ed.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $documentId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get documents by enrollment ID
     */
    public function getByEnrollment($enrollmentId)
    {
        $sql = "SELECT * FROM enrollment_documents 
                WHERE enrollment_id = :enrollment_id 
                ORDER BY document_type";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':enrollment_id' => $enrollmentId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Create or update document
     */
    public function createOrUpdate($enrollmentId, $documentType, $fileData)
    {
        $sql = "INSERT INTO enrollment_documents 
                (enrollment_id, document_type, original_filename, encrypted_filename, file_size, mime_type, encryption_key_id)
                VALUES (:enrollment_id, :document_type, :original_filename, :encrypted_filename, :file_size, :mime_type, :encryption_key_id)
                ON DUPLICATE KEY UPDATE 
                original_filename = VALUES(original_filename),
                encrypted_filename = VALUES(encrypted_filename),
                file_size = VALUES(file_size),
                mime_type = VALUES(mime_type),
                encryption_key_id = VALUES(encryption_key_id),
                uploaded_at = CURRENT_TIMESTAMP";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':enrollment_id' => $enrollmentId,
            ':document_type' => $documentType,
            ':original_filename' => $fileData['original_filename'],
            ':encrypted_filename' => $fileData['encrypted_filename'],
            ':file_size' => $fileData['file_size'],
            ':mime_type' => $fileData['mime_type'],
            ':encryption_key_id' => $fileData['encryption_key_id']
        ]);
    }

    /**
     * Delete document
     */
    public function delete($documentId)
    {
        $sql = "DELETE FROM enrollment_documents WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $documentId]);
    }

    /**
     * Check if document exists for enrollment and type
     */
    public function exists($enrollmentId, $documentType)
    {
        $sql = "SELECT COUNT(*) as count FROM enrollment_documents 
                WHERE enrollment_id = :enrollment_id AND document_type = :document_type";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':enrollment_id' => $enrollmentId,
            ':document_type' => $documentType
        ]);

        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count > 0;
    }

    /**
     * Get document count for enrollment
     */
    public function getDocumentCount($enrollmentId)
    {
        $sql = "SELECT COUNT(DISTINCT document_type) as count 
                FROM enrollment_documents 
                WHERE enrollment_id = :enrollment_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':enrollment_id' => $enrollmentId]);

        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }

    /**
     * Get required document types
     */
    public function getRequiredTypes()
    {
        return [
            'psa' => 'PSA Birth Certificate',
            'pwd_id' => 'PWD ID Card',
            'medical_record' => 'Medical Records',
            'beef' => 'Basic Education Enrollment Form (BEEF)'
        ];
    }

    /**
     * Validate document type
     */
    public function isValidDocumentType($documentType)
    {
        $validTypes = ['psa', 'pwd_id', 'medical_record', 'beef'];
        return in_array($documentType, $validTypes);
    }

    /**
     * Get documents grouped by type for enrollment
     */
    public function getDocumentsByType($enrollmentId)
    {
        $documents = $this->getByEnrollment($enrollmentId);
        $grouped = [];

        foreach ($documents as $doc) {
            $grouped[$doc->document_type] = $doc;
        }

        return $grouped;
    }
}
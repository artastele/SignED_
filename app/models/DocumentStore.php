<?php

class DocumentStore extends Model
{
    private $uploadPath;
    private $encryptionKey;
    
    public function __construct()
    {
        parent::__construct();
        $this->uploadPath = dirname(dirname(dirname(__FILE__))) . '/storage/documents/';
        $this->encryptionKey = $this->getEncryptionKey();
        
        // Create storage directory if it doesn't exist
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }
    
    /**
     * Store a file with AES-256 encryption
     * 
     * @param string $filePath Path to the original file
     * @param string $classification Security classification (public, internal, confidential, restricted)
     * @param int $userId User ID storing the document
     * @param string $documentType Type of document (enrollment, assessment, iep, etc.)
     * @return array Result with document_id and encrypted_filename
     */
    public function store($filePath, $classification, $userId, $documentType)
    {
        try {
            // Validate file exists
            if (!file_exists($filePath)) {
                throw new Exception("Source file does not exist");
            }
            
            // Read file content
            $fileContent = file_get_contents($filePath);
            if ($fileContent === false) {
                throw new Exception("Failed to read source file");
            }
            
            // Generate unique filename and encryption key ID
            $encryptedFilename = uniqid('doc_') . '_' . time() . '.enc';
            $keyId = uniqid('key_') . '_' . time();
            
            // Encrypt file content
            $encryptedContent = $this->encryptContent($fileContent, $keyId);
            
            // Store encrypted file
            $encryptedPath = $this->uploadPath . $encryptedFilename;
            if (file_put_contents($encryptedPath, $encryptedContent) === false) {
                throw new Exception("Failed to store encrypted file");
            }
            
            // Get file metadata
            $originalFilename = basename($filePath);
            $fileSize = filesize($filePath);
            $mimeType = mime_content_type($filePath);
            
            // Store document metadata in database
            $sql = "INSERT INTO document_store (
                        user_id, document_type, classification, 
                        original_filename, encrypted_filename, 
                        file_size, mime_type, encryption_key_id,
                        created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId, $documentType, $classification,
                $originalFilename, $encryptedFilename,
                $fileSize, $mimeType, $keyId
            ]);
            
            $documentId = $this->db->lastInsertId();
            
            // Log the storage action
            $this->logDocumentAction($userId, $documentId, 'store', $classification);
            
            return [
                'success' => true,
                'document_id' => $documentId,
                'encrypted_filename' => $encryptedFilename,
                'key_id' => $keyId
            ];
            
        } catch (Exception $e) {
            error_log("DocumentStore::store() Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Retrieve and decrypt a document
     * 
     * @param int $documentId Document ID to retrieve
     * @param int $userId User ID requesting the document
     * @param bool $applyWatermark Whether to apply watermark for restricted documents
     * @return array Result with file content or error
     */
    public function retrieve($documentId, $userId, $applyWatermark = true)
    {
        try {
            // Get document metadata
            $sql = "SELECT * FROM document_store WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$documentId]);
            $document = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$document) {
                throw new Exception("Document not found");
            }
            
            // Check access permissions
            if (!$this->checkAccess($documentId, $userId, 'read')) {
                throw new Exception("Access denied");
            }
            
            // Read encrypted file
            $encryptedPath = $this->uploadPath . $document['encrypted_filename'];
            if (!file_exists($encryptedPath)) {
                throw new Exception("Encrypted file not found");
            }
            
            $encryptedContent = file_get_contents($encryptedPath);
            if ($encryptedContent === false) {
                throw new Exception("Failed to read encrypted file");
            }
            
            // Decrypt content
            $decryptedContent = $this->decryptContent($encryptedContent, $document['encryption_key_id']);
            
            // Apply watermark for restricted documents
            if ($applyWatermark && in_array($document['classification'], ['restricted', 'confidential'])) {
                $decryptedContent = $this->applyWatermark($decryptedContent, $userId, $document);
            }
            
            // Log the access
            $this->logDocumentAction($userId, $documentId, 'retrieve', $document['classification']);
            
            return [
                'success' => true,
                'content' => $decryptedContent,
                'filename' => $document['original_filename'],
                'mime_type' => $document['mime_type'],
                'classification' => $document['classification']
            ];
            
        } catch (Exception $e) {
            error_log("DocumentStore::retrieve() Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Securely delete a document
     * 
     * @param int $documentId Document ID to delete
     * @param int $userId User ID requesting deletion
     * @return array Result of deletion operation
     */
    public function delete($documentId, $userId)
    {
        try {
            // Get document metadata
            $sql = "SELECT * FROM document_store WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$documentId]);
            $document = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$document) {
                throw new Exception("Document not found");
            }
            
            // Check delete permissions
            if (!$this->checkAccess($documentId, $userId, 'delete')) {
                throw new Exception("Delete access denied");
            }
            
            // Securely delete encrypted file
            $encryptedPath = $this->uploadPath . $document['encrypted_filename'];
            if (file_exists($encryptedPath)) {
                // Overwrite file with random data before deletion
                $fileSize = filesize($encryptedPath);
                $randomData = random_bytes($fileSize);
                file_put_contents($encryptedPath, $randomData);
                unlink($encryptedPath);
            }
            
            // Remove database record
            $sql = "DELETE FROM document_store WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$documentId]);
            
            // Log the deletion
            $this->logDocumentAction($userId, $documentId, 'delete', $document['classification']);
            
            return [
                'success' => true,
                'message' => 'Document securely deleted'
            ];
            
        } catch (Exception $e) {
            error_log("DocumentStore::delete() Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Apply watermark to restricted documents
     * 
     * @param string $content File content
     * @param int $userId User ID accessing the document
     * @param array $document Document metadata
     * @return string Watermarked content
     */
    public function applyWatermark($content, $userId, $document)
    {
        try {
            // Get user information
            $sql = "SELECT email, first_name, last_name FROM users WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return $content; // Return original if user not found
            }
            
            $watermarkText = sprintf(
                "CONFIDENTIAL - Downloaded by: %s %s (%s) on %s - Document ID: %d",
                $user['first_name'],
                $user['last_name'],
                $user['email'],
                date('Y-m-d H:i:s'),
                $document['id']
            );
            
            // For PDF files, add text watermark
            if ($document['mime_type'] === 'application/pdf') {
                return $this->addPdfWatermark($content, $watermarkText);
            }
            
            // For image files, add image watermark
            if (strpos($document['mime_type'], 'image/') === 0) {
                return $this->addImageWatermark($content, $watermarkText, $document['mime_type']);
            }
            
            // For other file types, return original content
            return $content;
            
        } catch (Exception $e) {
            error_log("DocumentStore::applyWatermark() Error: " . $e->getMessage());
            return $content; // Return original content on error
        }
    }
    
    /**
     * Encrypt content using AES-256-CBC
     * 
     * @param string $content Content to encrypt
     * @param string $keyId Encryption key identifier
     * @return string Encrypted content
     */
    private function encryptContent($content, $keyId)
    {
        $cipher = 'AES-256-CBC';
        $key = hash('sha256', $this->encryptionKey . $keyId, true);
        $iv = random_bytes(16);
        
        $encrypted = openssl_encrypt($content, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        
        if ($encrypted === false) {
            throw new Exception("Encryption failed");
        }
        
        // Prepend IV to encrypted data
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt content using AES-256-CBC
     * 
     * @param string $encryptedContent Encrypted content
     * @param string $keyId Encryption key identifier
     * @return string Decrypted content
     */
    private function decryptContent($encryptedContent, $keyId)
    {
        $cipher = 'AES-256-CBC';
        $key = hash('sha256', $this->encryptionKey . $keyId, true);
        
        $data = base64_decode($encryptedContent);
        if ($data === false) {
            throw new Exception("Invalid encrypted data");
        }
        
        // Extract IV and encrypted content
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        
        $decrypted = openssl_decrypt($encrypted, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        
        if ($decrypted === false) {
            throw new Exception("Decryption failed");
        }
        
        return $decrypted;
    }
    
    /**
     * Encrypt text data using AES-256-CBC
     * 
     * @param string $text Text to encrypt
     * @return string Encrypted text
     */
    public function encryptText($text)
    {
        if (empty($text)) {
            return $text;
        }
        
        try {
            $cipher = 'AES-256-CBC';
            $key = hash('sha256', $this->encryptionKey, true);
            $iv = random_bytes(16);
            
            $encrypted = openssl_encrypt($text, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            
            if ($encrypted === false) {
                throw new Exception("Text encryption failed");
            }
            
            // Prepend IV to encrypted data and encode
            return base64_encode($iv . $encrypted);
            
        } catch (Exception $e) {
            error_log("DocumentStore::encryptText() Error: " . $e->getMessage());
            return $text; // Return original text on error
        }
    }
    
    /**
     * Decrypt text data using AES-256-CBC
     * 
     * @param string $encryptedText Encrypted text
     * @return string Decrypted text
     */
    public function decryptText($encryptedText)
    {
        if (empty($encryptedText)) {
            return $encryptedText;
        }
        
        try {
            $cipher = 'AES-256-CBC';
            $key = hash('sha256', $this->encryptionKey, true);
            
            $data = base64_decode($encryptedText);
            if ($data === false) {
                throw new Exception("Invalid encrypted text data");
            }
            
            // Extract IV and encrypted content
            $iv = substr($data, 0, 16);
            $encrypted = substr($data, 16);
            
            $decrypted = openssl_decrypt($encrypted, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            
            if ($decrypted === false) {
                throw new Exception("Text decryption failed");
            }
            
            return $decrypted;
            
        } catch (Exception $e) {
            error_log("DocumentStore::decryptText() Error: " . $e->getMessage());
            return $encryptedText; // Return encrypted text on error
        }
    }
    
    /**
     * Get or generate encryption key
     * 
     * @return string Encryption key
     */
    private function getEncryptionKey()
    {
        $keyFile = dirname(dirname(dirname(__FILE__))) . '/config/encryption.key';
        
        if (file_exists($keyFile)) {
            return file_get_contents($keyFile);
        }
        
        // Generate new key
        $key = random_bytes(32);
        file_put_contents($keyFile, $key);
        chmod($keyFile, 0600); // Restrict access
        
        return $key;
    }
    
    /**
     * Check user access to document
     * 
     * @param int $documentId Document ID
     * @param int $userId User ID
     * @param string $action Action type (read, delete)
     * @return bool Access granted
     */
    private function checkAccess($documentId, $userId, $action)
    {
        // Get document and user information
        $sql = "SELECT ds.*, u.role 
                FROM document_store ds, users u 
                WHERE ds.id = ? AND u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$documentId, $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return false;
        }
        
        // Admin has access to all documents
        if ($result['role'] === 'admin') {
            return true;
        }
        
        // Users can access their own documents
        if ($result['user_id'] == $userId) {
            return true;
        }
        
        // SPED teachers can access learner-related documents
        if ($result['role'] === 'sped_teacher' && 
            in_array($result['document_type'], ['enrollment', 'assessment', 'iep', 'learning_material'])) {
            return true;
        }
        
        // Principals can access IEP documents
        if ($result['role'] === 'principal' && $result['document_type'] === 'iep') {
            return true;
        }
        
        // Guidance can access assessment and IEP documents
        if ($result['role'] === 'guidance' && 
            in_array($result['document_type'], ['assessment', 'iep'])) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Log document access action
     * 
     * @param int $userId User ID
     * @param int $documentId Document ID
     * @param string $action Action performed
     * @param string $classification Document classification
     */
    private function logDocumentAction($userId, $documentId, $action, $classification)
    {
        try {
            $sql = "INSERT INTO audit_logs (
                        user_id, action_type, entity_type, entity_id, 
                        additional_data, created_at
                    ) VALUES (?, 'document_access', 'document', ?, ?, NOW())";
            
            $additionalData = json_encode([
                'action' => $action,
                'classification' => $classification,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $documentId, $additionalData]);
            
        } catch (Exception $e) {
            error_log("DocumentStore::logDocumentAction() Error: " . $e->getMessage());
        }
    }
    
    /**
     * Add watermark to PDF content
     * 
     * @param string $content PDF content
     * @param string $watermarkText Watermark text
     * @return string Watermarked PDF content
     */
    private function addPdfWatermark($content, $watermarkText)
    {
        // For basic implementation, return original content
        // In production, use a PDF library like TCPDF or FPDF
        return $content;
    }
    
    /**
     * Add watermark to image content
     * 
     * @param string $content Image content
     * @param string $watermarkText Watermark text
     * @param string $mimeType Image MIME type
     * @return string Watermarked image content
     */
    private function addImageWatermark($content, $watermarkText, $mimeType)
    {
        try {
            // Check if GD extension is available
            if (!extension_loaded('gd')) {
                error_log("DocumentStore::addImageWatermark() Warning: GD extension not available. Returning original content.");
                return $content;
            }
            
            $image = imagecreatefromstring($content);
            if ($image === false) {
                return $content;
            }
            
            $width = imagesx($image);
            $height = imagesy($image);
            
            // Create watermark color (semi-transparent white)
            $watermarkColor = imagecolorallocatealpha($image, 255, 255, 255, 50);
            
            // Add watermark text
            $fontSize = min($width, $height) / 40;
            $x = 10;
            $y = $height - 20;
            
            imagestring($image, 2, $x, $y, $watermarkText, $watermarkColor);
            
            // Output watermarked image
            ob_start();
            switch ($mimeType) {
                case 'image/jpeg':
                    imagejpeg($image);
                    break;
                case 'image/png':
                    imagepng($image);
                    break;
                case 'image/gif':
                    imagegif($image);
                    break;
                default:
                    return $content;
            }
            $watermarkedContent = ob_get_contents();
            ob_end_clean();
            
            imagedestroy($image);
            
            return $watermarkedContent;
            
        } catch (Exception $e) {
            error_log("DocumentStore::addImageWatermark() Error: " . $e->getMessage());
            return $content;
        }
    }
}
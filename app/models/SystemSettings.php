<?php

class SystemSettings extends Model
{
    /**
     * Get setting by key
     */
    public function get($key, $default = null)
    {
        $sql = "SELECT setting_value, setting_type FROM system_settings WHERE setting_key = :key";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':key' => $key]);
        
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$result) {
            return $default;
        }
        
        // Convert value based on type
        return $this->convertValue($result->setting_value, $result->setting_type);
    }

    /**
     * Set setting value
     */
    public function set($key, $value, $type = 'string', $updatedBy = null)
    {
        // Convert value to string for storage
        $stringValue = $this->valueToString($value, $type);
        
        $sql = "INSERT INTO system_settings (setting_key, setting_value, setting_type, updated_by)
                VALUES (:key, :value, :type, :updated_by)
                ON DUPLICATE KEY UPDATE 
                    setting_value = VALUES(setting_value),
                    setting_type = VALUES(setting_type),
                    updated_by = VALUES(updated_by),
                    updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':key' => $key,
            ':value' => $stringValue,
            ':type' => $type,
            ':updated_by' => $updatedBy
        ]);
    }

    /**
     * Get all settings
     */
    public function getAll()
    {
        $sql = "SELECT * FROM system_settings ORDER BY setting_key";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $settings = [];
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        foreach ($results as $row) {
            $settings[$row->setting_key] = [
                'value' => $this->convertValue($row->setting_value, $row->setting_type),
                'type' => $row->setting_type,
                'description' => $row->description
            ];
        }
        
        return $settings;
    }

    /**
     * Get settings by category (prefix)
     */
    public function getByCategory($prefix)
    {
        $sql = "SELECT * FROM system_settings WHERE setting_key LIKE :prefix ORDER BY setting_key";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':prefix' => $prefix . '%']);
        
        $settings = [];
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        foreach ($results as $row) {
            $settings[$row->setting_key] = [
                'value' => $this->convertValue($row->setting_value, $row->setting_type),
                'type' => $row->setting_type,
                'description' => $row->description
            ];
        }
        
        return $settings;
    }

    /**
     * Update multiple settings at once
     */
    public function updateBatch($settings, $updatedBy = null)
    {
        try {
            $this->db->beginTransaction();
            
            foreach ($settings as $key => $data) {
                $value = $data['value'] ?? $data;
                $type = $data['type'] ?? 'string';
                
                $result = $this->set($key, $value, $type, $updatedBy);
                
                if (!$result) {
                    error_log("Failed to update setting: $key");
                    throw new Exception("Failed to update setting: $key");
                }
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("SystemSettings::updateBatch error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete setting
     */
    public function delete($key)
    {
        $sql = "DELETE FROM system_settings WHERE setting_key = :key";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':key' => $key]);
    }

    /**
     * Convert stored string value to proper type
     */
    private function convertValue($value, $type)
    {
        switch ($type) {
            case 'number':
                return is_numeric($value) ? (float)$value : 0;
            
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            
            case 'json':
                return json_decode($value, true);
            
            case 'string':
            default:
                return $value;
        }
    }

    /**
     * Convert value to string for storage
     */
    private function valueToString($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            
            case 'json':
                return json_encode($value);
            
            case 'number':
            case 'string':
            default:
                return (string)$value;
        }
    }

    /**
     * Get session timeout in minutes
     */
    public function getSessionTimeout()
    {
        return $this->get('session_timeout', 30);
    }

    /**
     * Get max login attempts
     */
    public function getMaxLoginAttempts()
    {
        return $this->get('max_login_attempts', 5);
    }

    /**
     * Check if email verification is required
     */
    public function requireEmailVerification()
    {
        return $this->get('require_email_verification', true);
    }

    /**
     * Check if audit logging is enabled
     */
    public function isAuditLoggingEnabled()
    {
        return $this->get('enable_audit_logging', true);
    }
}

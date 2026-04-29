<?php

class Announcement extends Model
{
    /**
     * Create new announcement
     */
    public function create($data)
    {
        $sql = "INSERT INTO announcements (title, content, priority, target_audience, created_by, expires_at)
                VALUES (:title, :content, :priority, :target_audience, :created_by, :expires_at)";
        
        $stmt = $this->db->prepare($sql);
        
        $result = $stmt->execute([
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':priority' => $data['priority'] ?? 'normal',
            ':target_audience' => $data['target_audience'] ?? 'all',
            ':created_by' => $data['created_by'],
            ':expires_at' => $data['expires_at'] ?? null
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Update announcement
     */
    public function update($id, $data)
    {
        $sql = "UPDATE announcements 
                SET title = :title,
                    content = :content,
                    priority = :priority,
                    target_audience = :target_audience,
                    expires_at = :expires_at,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':priority' => $data['priority'],
            ':target_audience' => $data['target_audience'],
            ':expires_at' => $data['expires_at'] ?? null,
            ':id' => $id
        ]);
    }

    /**
     * Delete announcement
     */
    public function delete($id)
    {
        $sql = "DELETE FROM announcements WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Toggle announcement active status
     */
    public function toggleActive($id)
    {
        $sql = "UPDATE announcements 
                SET is_active = NOT is_active,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get announcement by ID
     */
    public function getById($id)
    {
        $sql = "SELECT a.*, u.fullname as created_by_name
                FROM announcements a
                LEFT JOIN users u ON a.created_by = u.id
                WHERE a.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get all announcements (admin view)
     */
    public function getAll()
    {
        $sql = "SELECT a.*, u.fullname as created_by_name
                FROM announcements a
                LEFT JOIN users u ON a.created_by = u.id
                ORDER BY a.priority DESC, a.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get active announcements for specific user role
     */
    public function getForUser($userRole)
    {
        $sql = "SELECT a.*, u.fullname as created_by_name
                FROM announcements a
                LEFT JOIN users u ON a.created_by = u.id
                WHERE a.is_active = TRUE
                AND (a.expires_at IS NULL OR a.expires_at > NOW())
                AND (a.target_audience = 'all' OR a.target_audience = :role)
                ORDER BY 
                    CASE a.priority
                        WHEN 'urgent' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'normal' THEN 3
                        WHEN 'low' THEN 4
                    END,
                    a.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        
        // Map user roles to target audiences
        $targetRole = $this->mapRoleToTarget($userRole);
        
        $stmt->execute([':role' => $targetRole]);
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get unread announcements for user
     */
    public function getUnreadForUser($userId, $userRole)
    {
        $sql = "SELECT a.*, u.fullname as created_by_name
                FROM announcements a
                LEFT JOIN users u ON a.created_by = u.id
                LEFT JOIN announcement_reads ar ON a.id = ar.announcement_id AND ar.user_id = :user_id
                WHERE a.is_active = TRUE
                AND (a.expires_at IS NULL OR a.expires_at > NOW())
                AND (a.target_audience = 'all' OR a.target_audience = :role)
                AND ar.id IS NULL
                ORDER BY 
                    CASE a.priority
                        WHEN 'urgent' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'normal' THEN 3
                        WHEN 'low' THEN 4
                    END,
                    a.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        
        $targetRole = $this->mapRoleToTarget($userRole);
        
        $stmt->execute([
            ':user_id' => $userId,
            ':role' => $targetRole
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Mark announcement as read by user
     */
    public function markAsRead($announcementId, $userId)
    {
        $sql = "INSERT INTO announcement_reads (announcement_id, user_id)
                VALUES (:announcement_id, :user_id)
                ON DUPLICATE KEY UPDATE read_at = CURRENT_TIMESTAMP";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':announcement_id' => $announcementId,
            ':user_id' => $userId
        ]);
    }

    /**
     * Get announcement statistics
     */
    public function getStats($announcementId)
    {
        $sql = "SELECT 
                    COUNT(DISTINCT ar.user_id) as read_count,
                    (SELECT COUNT(*) FROM users WHERE role IN (
                        CASE 
                            WHEN a.target_audience = 'all' THEN role
                            WHEN a.target_audience = 'parents' THEN 'parent'
                            WHEN a.target_audience = 'teachers' THEN 'teacher'
                            WHEN a.target_audience = 'sped_staff' THEN role
                            WHEN a.target_audience = 'learners' THEN 'learner'
                            WHEN a.target_audience = 'admins' THEN 'admin'
                        END
                    )) as target_count
                FROM announcements a
                LEFT JOIN announcement_reads ar ON a.id = ar.announcement_id
                WHERE a.id = :id
                GROUP BY a.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $announcementId]);
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get count of active announcements
     */
    public function getActiveCount()
    {
        $sql = "SELECT COUNT(*) as count 
                FROM announcements 
                WHERE is_active = TRUE 
                AND (expires_at IS NULL OR expires_at > NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }

    /**
     * Map user role to target audience
     */
    private function mapRoleToTarget($userRole)
    {
        $mapping = [
            'parent' => 'parents',
            'teacher' => 'teachers',
            'sped_teacher' => 'sped_staff',
            'guidance' => 'sped_staff',
            'principal' => 'sped_staff',
            'learner' => 'learners',
            'admin' => 'admins'
        ];
        
        return $mapping[$userRole] ?? 'all';
    }

    /**
     * Clean up expired announcements
     */
    public function cleanupExpired()
    {
        $sql = "UPDATE announcements 
                SET is_active = FALSE 
                WHERE expires_at IS NOT NULL 
                AND expires_at < NOW() 
                AND is_active = TRUE";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute();
    }
}

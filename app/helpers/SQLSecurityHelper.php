<?php

/**
 * SQL Security Helper
 * 
 * This class provides utilities for preventing SQL injection attacks
 * and ensuring all database operations use parameterized queries.
 * 
 * Requirements: 16.3, 16.6
 */
class SQLSecurityHelper
{
    private $auditLog;
    
    public function __construct()
    {
        $this->auditLog = new AuditLog();
    }
    
    /**
     * Execute a secure parameterized query
     * 
     * @param PDO $db Database connection
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @param int|null $userId User ID for logging
     * @param string $operation Operation description
     * @return array Query result
     */
    public function executeSecureQuery($db, $sql, $params = [], $userId = null, $operation = 'database_query')
    {
        try {
            // Validate SQL query for suspicious patterns
            if (!$this->validateSQLQuery($sql, $userId)) {
                throw new Exception("SQL query failed security validation");
            }
            
            // Validate parameters
            $validatedParams = $this->validateParameters($params, $userId);
            
            // Prepare and execute query
            $stmt = $db->prepare($sql);
            
            if (!$stmt) {
                throw new PDOException("Failed to prepare statement: " . implode(', ', $db->errorInfo()));
            }
            
            $result = $stmt->execute($validatedParams);
            
            if (!$result) {
                throw new PDOException("Query execution failed: " . implode(', ', $stmt->errorInfo()));
            }
            
            // Log successful query execution for audit
            $this->logQueryExecution($sql, $validatedParams, $userId, $operation, true);
            
            return [
                'success' => true,
                'statement' => $stmt,
                'affected_rows' => $stmt->rowCount()
            ];
            
        } catch (Exception $e) {
            // Log failed query execution
            $this->logQueryExecution($sql, $params, $userId, $operation, false, $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode()
            ];
        }
    }
    
    /**
     * Execute a secure SELECT query and return results
     * 
     * @param PDO $db Database connection
     * @param string $sql SQL SELECT query
     * @param array $params Query parameters
     * @param int|null $userId User ID for logging
     * @param int $fetchMode PDO fetch mode
     * @return array Query result with data
     */
    public function executeSecureSelect($db, $sql, $params = [], $userId = null, $fetchMode = PDO::FETCH_ASSOC)
    {
        $result = $this->executeSecureQuery($db, $sql, $params, $userId, 'select');
        
        if (!$result['success']) {
            return $result;
        }
        
        try {
            $data = $result['statement']->fetchAll($fetchMode);
            
            return [
                'success' => true,
                'data' => $data,
                'row_count' => count($data)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to fetch query results: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Execute a secure INSERT query and return the inserted ID
     * 
     * @param PDO $db Database connection
     * @param string $sql SQL INSERT query
     * @param array $params Query parameters
     * @param int|null $userId User ID for logging
     * @return array Query result with inserted ID
     */
    public function executeSecureInsert($db, $sql, $params = [], $userId = null)
    {
        $result = $this->executeSecureQuery($db, $sql, $params, $userId, 'insert');
        
        if (!$result['success']) {
            return $result;
        }
        
        return [
            'success' => true,
            'inserted_id' => $db->lastInsertId(),
            'affected_rows' => $result['affected_rows']
        ];
    }
    
    /**
     * Execute a secure UPDATE query
     * 
     * @param PDO $db Database connection
     * @param string $sql SQL UPDATE query
     * @param array $params Query parameters
     * @param int|null $userId User ID for logging
     * @return array Query result
     */
    public function executeSecureUpdate($db, $sql, $params = [], $userId = null)
    {
        $result = $this->executeSecureQuery($db, $sql, $params, $userId, 'update');
        
        if (!$result['success']) {
            return $result;
        }
        
        return [
            'success' => true,
            'affected_rows' => $result['affected_rows']
        ];
    }
    
    /**
     * Execute a secure DELETE query
     * 
     * @param PDO $db Database connection
     * @param string $sql SQL DELETE query
     * @param array $params Query parameters
     * @param int|null $userId User ID for logging
     * @return array Query result
     */
    public function executeSecureDelete($db, $sql, $params = [], $userId = null)
    {
        $result = $this->executeSecureQuery($db, $sql, $params, $userId, 'delete');
        
        if (!$result['success']) {
            return $result;
        }
        
        return [
            'success' => true,
            'affected_rows' => $result['affected_rows']
        ];
    }
    
    /**
     * Validate SQL query for suspicious patterns
     * 
     * @param string $sql SQL query
     * @param int|null $userId User ID for logging
     * @return bool Query is safe
     */
    private function validateSQLQuery($sql, $userId = null)
    {
        // Check for dangerous SQL patterns
        $dangerousPatterns = [
            // Multiple statements
            '/;\s*(DROP|DELETE|TRUNCATE|ALTER|CREATE|INSERT|UPDATE)\s+/i',
            // Union-based injection
            '/UNION\s+SELECT/i',
            // Comment-based injection
            '/\/\*.*\*\//s',
            '/--\s*[^\r\n]*/i',
            // Stored procedure execution (fixed pattern)
            '/;\s*EXEC(?:UTE)?\s*[\(\s]/i',
            // Information schema queries
            '/information_schema/i',
            // System functions
            '/@@version|@@servername|@@hostname/i',
            // Hex encoding attempts
            '/0x[0-9a-f]+/i',
            // Benchmark/sleep functions (time-based injection)
            '/benchmark\s*\(|sleep\s*\(/i'
        ];
        
        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $sql)) {
                // Log suspicious SQL attempt
                $this->auditLog->logError(
                    'security',
                    'critical',
                    "Potentially dangerous SQL pattern detected",
                    null,
                    [
                        'sql_pattern' => $pattern,
                        'query' => substr($sql, 0, 200),
                        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                    ],
                    $userId
                );
                return false;
            }
        }
        
        // Check for excessive query length (potential buffer overflow)
        if (strlen($sql) > 10000) {
            $this->auditLog->logError(
                'security',
                'high',
                "Excessively long SQL query detected",
                null,
                ['query_length' => strlen($sql)],
                $userId
            );
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate and sanitize query parameters
     * 
     * @param array $params Query parameters
     * @param int|null $userId User ID for logging
     * @return array Validated parameters
     */
    private function validateParameters($params, $userId = null)
    {
        $validated = [];
        
        foreach ($params as $key => $value) {
            // Check for null byte injection
            if (is_string($value) && strpos($value, "\0") !== false) {
                $this->auditLog->logError(
                    'security',
                    'critical',
                    "Null byte injection attempt in SQL parameter",
                    null,
                    ['parameter' => $key, 'value' => substr($value, 0, 100)],
                    $userId
                );
                throw new Exception("Invalid characters in parameter: $key");
            }
            
            // Check for excessively long string parameters
            if (is_string($value) && strlen($value) > 65535) {
                $this->auditLog->logError(
                    'security',
                    'medium',
                    "Excessively long parameter value",
                    null,
                    ['parameter' => $key, 'length' => strlen($value)],
                    $userId
                );
                throw new Exception("Parameter value too long: $key");
            }
            
            $validated[$key] = $value;
        }
        
        return $validated;
    }
    
    /**
     * Log query execution for audit purposes
     * 
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @param int|null $userId User ID
     * @param string $operation Operation type
     * @param bool $success Execution success
     * @param string|null $error Error message if failed
     */
    private function logQueryExecution($sql, $params, $userId, $operation, $success, $error = null)
    {
        // Only log for audit purposes, not every query to avoid log spam
        $auditableOperations = ['insert', 'update', 'delete'];
        
        if (!in_array($operation, $auditableOperations) && $success) {
            return; // Don't log successful SELECT queries
        }
        
        $logData = [
            'operation' => $operation,
            'success' => $success,
            'query_hash' => hash('sha256', $sql), // Hash instead of full query for privacy
            'param_count' => count($params),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        if (!$success && $error) {
            $logData['error'] = $error;
        }
        
        $this->auditLog->logError(
            'database',
            $success ? 'low' : 'medium',
            $success ? "Database operation completed: $operation" : "Database operation failed: $operation",
            null,
            $logData,
            $userId
        );
    }
    
    /**
     * Escape identifier names (table names, column names) for dynamic queries
     * 
     * @param string $identifier Identifier to escape
     * @return string Escaped identifier
     */
    public function escapeIdentifier($identifier)
    {
        // Remove any non-alphanumeric characters except underscores
        $escaped = preg_replace('/[^a-zA-Z0-9_]/', '', $identifier);
        
        // Ensure it doesn't start with a number
        if (preg_match('/^[0-9]/', $escaped)) {
            throw new Exception("Invalid identifier: cannot start with number");
        }
        
        // Check against reserved words
        $reservedWords = [
            'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'CREATE', 'ALTER',
            'TABLE', 'DATABASE', 'INDEX', 'VIEW', 'PROCEDURE', 'FUNCTION',
            'TRIGGER', 'USER', 'GRANT', 'REVOKE', 'UNION', 'WHERE', 'ORDER',
            'GROUP', 'HAVING', 'LIMIT', 'OFFSET', 'JOIN', 'INNER', 'LEFT',
            'RIGHT', 'OUTER', 'ON', 'AS', 'AND', 'OR', 'NOT', 'NULL', 'TRUE',
            'FALSE', 'CASE', 'WHEN', 'THEN', 'ELSE', 'END', 'IF', 'EXISTS'
        ];
        
        if (in_array(strtoupper($escaped), $reservedWords)) {
            throw new Exception("Invalid identifier: reserved word");
        }
        
        return '`' . $escaped . '`';
    }
    
    /**
     * Build a secure WHERE clause with parameterized conditions
     * 
     * @param array $conditions Associative array of column => value conditions
     * @param string $operator Logical operator (AND, OR)
     * @return array WHERE clause and parameters
     */
    public function buildSecureWhereClause($conditions, $operator = 'AND')
    {
        if (empty($conditions)) {
            return ['where' => '', 'params' => []];
        }
        
        $operator = strtoupper($operator);
        if (!in_array($operator, ['AND', 'OR'])) {
            throw new Exception("Invalid logical operator: $operator");
        }
        
        $whereParts = [];
        $params = [];
        $paramIndex = 0;
        
        foreach ($conditions as $column => $value) {
            // Validate column name
            $escapedColumn = $this->escapeIdentifier($column);
            
            if (is_array($value)) {
                // Handle IN clause
                $placeholders = [];
                foreach ($value as $item) {
                    $paramName = ":param_" . $paramIndex++;
                    $placeholders[] = $paramName;
                    $params[$paramName] = $item;
                }
                $whereParts[] = "$escapedColumn IN (" . implode(', ', $placeholders) . ")";
            } elseif ($value === null) {
                // Handle NULL values
                $whereParts[] = "$escapedColumn IS NULL";
            } else {
                // Handle regular equality
                $paramName = ":param_" . $paramIndex++;
                $whereParts[] = "$escapedColumn = $paramName";
                $params[$paramName] = $value;
            }
        }
        
        $whereClause = 'WHERE ' . implode(" $operator ", $whereParts);
        
        return [
            'where' => $whereClause,
            'params' => $params
        ];
    }
    
    /**
     * Get database query statistics for monitoring
     * 
     * @param PDO $db Database connection
     * @param int $days Number of days to analyze
     * @return array Query statistics
     */
    public function getQueryStatistics($db, $days = 7)
    {
        try {
            $sql = "SELECT 
                        DATE(created_at) as query_date,
                        JSON_EXTRACT(additional_data, '$.operation') as operation,
                        COUNT(*) as query_count,
                        SUM(CASE WHEN JSON_EXTRACT(additional_data, '$.success') = true THEN 1 ELSE 0 END) as successful_queries,
                        SUM(CASE WHEN JSON_EXTRACT(additional_data, '$.success') = false THEN 1 ELSE 0 END) as failed_queries
                    FROM audit_logs 
                    WHERE action_type = 'database' 
                      AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    GROUP BY DATE(created_at), JSON_EXTRACT(additional_data, '$.operation')
                    ORDER BY query_date DESC, query_count DESC";
            
            $result = $this->executeSecureSelect($db, $sql, [$days]);
            
            return $result['success'] ? $result['data'] : [];
            
        } catch (Exception $e) {
            error_log("Failed to get query statistics: " . $e->getMessage());
            return [];
        }
    }
}
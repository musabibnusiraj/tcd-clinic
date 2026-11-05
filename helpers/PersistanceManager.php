<?php

/**
 * Project Name: Tooth Care - Channeling Appointments
 * Author: Musab Ibn Siraj
 */

/**
 * PersistanceManager - the ONLY class that talks to the database.
 *
 * It is a thin wrapper around PDO. Every query uses "prepared statements",
 * which means values are sent separately from the SQL text. This protects
 * the application from SQL injection.
 *
 * Example:
 *   $pm->fetchOne("SELECT * FROM users WHERE id = :id", [':id' => 5]);
 *
 * The database schema (tables) is in db/tooth_care.sql.
 */
class PersistanceManager
{
    /** @var PDO The database connection */
    private $pdo;

    /**
     * Opens the database connection.
     * Throws a PDOException if the connection fails.
     */
    public function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $this->pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);

        // Throw exceptions on SQL errors (instead of failing silently)
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Use real prepared statements
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    /**
     * Runs a SELECT query and returns ALL rows.
     *
     * @param string $sql    SQL text with :placeholders
     * @param array  $params Values for the placeholders, e.g. [':id' => 1]
     * @return array List of rows (empty array when nothing found)
     */
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Runs a SELECT query and returns only the FIRST row.
     *
     * @return array|false The row, or false when nothing found
     */
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Runs an UPDATE / DELETE query (or any query that returns no rows).
     *
     * @return int Number of rows affected
     */
    public function execute($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Runs an INSERT query.
     *
     * @return int The auto-increment id of the new row
     */
    public function insert($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $this->pdo->lastInsertId();
    }
}

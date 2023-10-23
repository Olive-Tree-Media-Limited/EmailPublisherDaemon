<?php

require_once 'SQLException.php';

/**
 * MySQL database utilities class.
 *
 * @package MySQL_Database_Utilities
 * @license GNU Lesser General Public License
 * @since Version 1.0
 */
class DatabaseUtilities
{
    /**
     * Sets the connection's auto-commit mode to the given state.
     *
     * This function doesn't work with non transactional table types (like
     * MyISAM or ISAM).
     *
     * @param object $mysqli the mysqli connection
     * @param bool $bool TRUE to set auto-commit on, FALSE otherwise
     *
     * @static
     */
    public static function setAutoCommit($mysqli, $bool)
    {
        if ($mysqli->autocommit($bool) === FALSE) {
            throw new SQLException($mysqli->error);
        }
    }

    /**
     * Retrieves the current auto-commit mode for this Connection.
     *
     * @param object $mysqli the mysqli connection
     *
     * @return mixed TRUE if auto-commit is on, FALSE if it is off, NULL if it
     *               cannot be determined
     *
     * @static
     */
    public static function getAutoCommit($mysqli)
    {
        $mode = NULL;

        $result = $mysqli->query("SELECT @@autocommit");
        if ($result === FALSE) {
            throw new SQLException($mysqli->error);
        }

        while (TRUE) {
            $row = $result->fetch_row();
            
            if ($row === NULL) {
                break;
            }

            $mode = (bool) $row[0];
        }

        $result->free();

        return $mode;
    }

    /**
     * Makes all changes made since the previous commit/rollback permanent.
     *
     * @param object $mysqli the mysqli connection
     *
     * @static
     */
    public static function commit($mysqli)
    {
        if ($mysqli->commit() === FALSE) {
            throw new SQLException($mysqli->error);
        }
    }

    /**
     * Undoes all changes made in the current transaction.
     *
     * @param object $mysqli the mysqli connection
     *
     * @static
     */
    public static function rollback($mysqli)
    {
        if ($mysqli->rollback() === FALSE) {
            throw new SQLException($mysqli->error);
        }
    }

    /**
     * Prepares a MySQL statement for execution.
     * 
     * @param object $mysqli the MySQL connection object
     * @param string $query the query to prepare
     *
     * @return object the prepared MySQL statement object
     *
     * @static
     */
    public static function prepareStatement($mysqli, $query)
    {
        $stmt = $mysqli->prepare($query);
        if ($stmt === FALSE) {
            throw new SQLException($mysqli->error);
        }

        return $stmt;
    }

    /**
     * Closes the specified prepared statement. Also deallocates the statement
     * handle. If the current statement has pending or unread results, this
     * function cancels them so that the next query can be executed.
     *
     * @param object $stmt the prepared statement
     *
     * @return bool TRUE on success
     *
     * @static
     */
    public static function closeStatement($stmt)
    {
        if ($stmt->close() === FALSE) {
            throw new SQLException($stmt->error);
        }

        return TRUE;
    }

    /**
     * Executes a prepared statement.
     *
     * @param object $stmt the prepared statement
     *
     * @return bool TRUE on success
     *
     * @static
     */
    public static function executeStatement($stmt)
    {
        if ($stmt->execute() === FALSE) {
            throw new SQLException($stmt->error);
        }

        return TRUE;
    }

    /**
     * Executes an INSERT query. The query may be parameterized.
     *
     * @param object $mysqli the MySQL connection object
     * @param string $query the sql statement
     * @param array $params the parameters to be bound or NULL
     *
     * @return void
     *
     * @static
     */
    public static function insert($mysqli, $query, $params = NULL)
    {
        DatabaseUtilities::exec($mysqli, $query, $params);
    }

    /**
     * Executes a DELETE query. The query may be parameterized.
     *
     * @param object $mysqli the MySQL connection object
     * @param string $query the sql statement
     * @param array $params the parameters to be bound or NULL
     *
     * @return void
     *
     * @static
     */
    public static function delete($mysqli, $query, $params = NULL)
    {
        DatabaseUtilities::exec($mysqli, $query, $params);
    }

    /**
     * Executes an UPDATE query. The query may be parameterized.
     *
     * @param object $mysqli the MySQL connection object
     * @param string $query the sql statement
     * @param array $params the parameters to be bound or NULL
     *
     * @return void
     *
     * @static
     */
    public static function update($mysqli, $query, $params = NULL)
    {
        return DatabaseUtilities::exec($mysqli, $query, $params);
    }

    /**
     * Executes an INSERT, UPDATE or DELETE query.
     *
     * @param object $mysqli the MySQL connection object
     * @param string $query the sql statement
     * @param array $params the parameters to be bound or NULL
     *
     * @return void
     *
     * @static
     */
    private static function exec($mysqli, $query, $params = NULL)
    {
        $stmt = DatabaseUtilities::prepareStatement($mysqli, $query);

        if ($params !== NULL) {
            DatabaseUtilities::bindParameters($stmt, $params);
        }

        $result1=DatabaseUtilities::executeStatement($stmt);
        $result2=DatabaseUtilities::closeStatement($stmt);

        return $result1;
    }

    /**
     * Executes a SELECT statement, binds the parameters and puts the results
     * into an associative array.
     *
     * @param object $mysqli the MySQL connection object
     * @param string $query the sql statement
     * @param array $bindParams the parameters to bind or NULL
     *
     * @return mixed an associative array that holds the data or NULL
     *
     * @static
     */
    public static function executeQuery($mysqli, $query, $bindParams = NULL)
    {
        $result = Array();

        $stmt = DatabaseUtilities::prepareStatement($mysqli, $query);
        if ($bindParams !== NULL) {
            DatabaseUtilities::bindParameters($stmt, $bindParams);
        }
        DatabaseUtilities::executeStatement($stmt);

        $fieldNames = DatabaseUtilities::fetchFields($stmt);
        if (!empty($fieldNames)) {
            $bindResult = Array();

            foreach ($fieldNames as $fieldName) {
                // Make the field name into a variable to be bound
                $bindResult[] = &${$fieldName};
            }

            DatabaseUtilities::bindResult($stmt, $bindResult);

            $i = 0;
            while (TRUE) {
                $res = $stmt->fetch();
                if ($res === FALSE) {
                    throw new SQLException($stmt->error);
                }

                if ($res === NULL) {
                    break;
                }

                foreach ($fieldNames as $fieldName) {
                    $result[$i][$fieldName] = ${$fieldName};
                }

                $i++;
            }
        }

        DatabaseUtilities::closeStatement($stmt);

        return $result;
    }

    /**
     * Returns an array of the field names.
     *
     * @param object $stmt the MySQL statement object
     *
     * @return array an array of the field names
     *
     * @static
     */
    public static function fetchFields($stmt)
    {
        $metadata = $stmt->result_metadata();
        $fieldNames = Array();

        if ($metadata !== FALSE) {
            while ($field = $metadata->fetch_field()) {
                $fieldNames[] = $field->name;
            }
        }

        return $fieldNames;
    }

    /**
     * Binds the parameters in the params array to the sql statement.
     *
     * @param string $stmt the sql statement
     * @param array $params the array of parameters, e.g.<br>
     *                      $params = array("si", $str, $num);
     *
     * @return bool TRUE on success, FALSE if there are no parameters to bind
     *
     * @static
     */
    public static function bindParameters(&$stmt, $params)
    {
        $parametersBound = FALSE;

        if (!empty($params)) {
            if (call_user_func_array(array(&$stmt, 'bind_param'), $params)
                    === FALSE) {
                throw new SQLException($stmt->error);
            }

            $parametersBound = TRUE;
        }

        return $parametersBound;
    }

    /**
     * Binds the variables in the $bindResult array to a prepared statement for
     * result storage.
     *
     * @param object $stmt the MySQL statement object
     * @param array $bindResult the array that will contain the field names
     *
     * @return bool TRUE on success
     *
     * @static
     */
    public static function bindResult($stmt, $bindResult)
    {
        if (!empty($bindResult)) {
            if (call_user_func_array(Array($stmt, "bind_result"), $bindResult)
                    === FALSE) {
                throw new SQLException($stmt->error);
            }
        }

        return TRUE;
    }

    /**
     * Selects the database to be used when performing queries.
     * <br>
     * <b>Note:</b> This function should only be used to change the default
     * database for the connection. You should select the default database with
     * 4th parameter when connecting.
     *
     * @param object $mysqli the MySQL connection object
     * @param string $name the name of the new database
     */
    public static function setDatabase($mysqli, $name)
    {
        if ($mysqli->select_db($name) === FALSE) {
            throw new SQLException($mysqli->error);
        }
    }

    /**
     * Gets the number of affected rows in a MySQL operation.
     * <br>
     * For SELECT statements mysqli_affected_rows() works like mysqli_num_rows()
     *
     * @param object $mysqli the MySQL connection object
     *
     * @return int an integer greater than zero indicates the number of rows
     *             affected or retrieved. Zero indicates that no records where
     *             updated for an UPDATE statement, no rows matched the WHERE
     *             clause in the query or that no query has yet been executed.
     *             -1 indicates that the query returned an error.
     */
    public static function getAffectedRows($mysqli)
    {
        return $mysqli->affected_rows;
    }

    /**
     * Gets the number of columns for the most recent query.
     *
     * @param object $mysqli the MySQL connection object
     *
     * @return int an integer representing the number of fields in a result set
     */
    public static function getFieldCount($mysqli)
    {
        return $mysqli->field_count;
    }

    /**
     * Gets the current character set.
     *
     * @param object $mysqli the MySQL connection object
     *
     * @return string the current character set
     */
    public static function getCharacterSet($mysqli)
    {
        return $mysqli->get_charset()->charset;
    }

    /**
     * Sets a new character set.
     *
     * @param object $mysqli the MySQL connection object
     *
     * @return bool TRUE on success
     */
    public static function setCharacterSet($mysqli, $charset)
    {
        if ($mysqli->set_charset($charset) === FALSE) {
            throw new SQLException($mysqli->error);
        }

        return TRUE;
    }

    /**
     * Gets the current character set collation.
     *
     * @param object $mysqli the MySQL connection object
     *
     * @return string the current character set collation
     */
    public static function getCollation($mysqli)
    {
        return $mysqli->get_charset()->collation;
    }

    /**
     * Gets information about the most recently executed query.
     *
     * @param object $mysqli the MySQL connection object
     *
     * @return string information about the most recently executed query
     */
    public static function getLastQueryInfo($mysqli)
    {
        return $mysqli->info;
    }

    /**
     * Gets the auto generated id used in the last query.
     *
     * @param object $mysqli the MySQL connection object
     *
     * @return int the ID generated by a query on a table with a column having
     *             the AUTO_INCREMENT attribute. If the last query wasn't an
     *             INSERT or UPDATE statement or if the modified table does not
     *             have a column with the AUTO_INCREMENT attribute, this
     *             function will return zero
     */
    public static function getInsertID($mysqli)
    {
        return $mysqli->insert_id;
    }

    /**
     * Gets any warnings from the last query execution.
     *
     * @param object $mysqli the MySQL connection object
     *
     * @return array an array of the Level, Code and Message of each warning
     */
    public static function getWarnings($mysqli)
    {
        $warnings = array();

        $result = $mysqli->query("SHOW WARNINGS");
        if ($result === FALSE) {
            throw new SQLException($mysqli->error);
        }

        while (TRUE) {
            $row = $result->fetch_row();

            if ($row === NULL) {
                break;
            }

            $warnings[] = $row;
        }

        $result->free();

        return $warnings;
    }

    /**
     * Frees stored result memory for the given statement handle.
     *
     * @param object $stmt the MySQL statement object
     */
    public static function freeResult($stmt)
    {
        $stmt->free_result;
    }
}
?>
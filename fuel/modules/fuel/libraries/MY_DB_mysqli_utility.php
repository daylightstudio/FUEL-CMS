<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/* Thanks to Gumster
http://forum.getfuelcms.com/discussion/2031/mysqli-the-fuel-backup-module#Item_4
*/

class MY_DB_mysqli_utility extends CI_DB_utility {

    function __construct() {
        parent::__construct();
        log_message('debug', 'Extended DB mysqli utility class instantiated!');
    }

    /**
     * List databases
     *
     * @access	private
     * @return	bool
     */
    function _list_databases() {
        return "SHOW DATABASES";
    }

    // --------------------------------------------------------------------

    /**
     * Optimize table query
     *
     * Generates a platform-specific query so that a table can be optimized
     *
     * @access	private
     * @param	string	the table name
     * @return	object
     */
    function _optimize_table($table) {
        return "OPTIMIZE TABLE " . $this->db->_escape_identifiers($table);
    }

    // --------------------------------------------------------------------

    /**
     * Repair table query
     *
     * Generates a platform-specific query so that a table can be repaired
     *
     * @access	private
     * @param	string	the table name
     * @return	object
     */
    function _repair_table($table) {
        return "REPAIR TABLE " . $this->db->_escape_identifiers($table);
    }

    /**
     * Backup
     *
     * @param	array	$params	Preferences
     * @return	mixed
     */
    function _backup($params = array()) {

        if (count($params) === 0) {
            return FALSE;
        }
        // Extract the prefs for simplicity
        extract($params);
        // Build the output
        $output = '';

        foreach ((array) $tables as $table) {
            // Is the table in the "ignore" list?
            if (in_array($table, (array) $ignore, TRUE)) {
                continue;
            }
            // Get the table schema
            $query = $this->db->query('SHOW CREATE TABLE ' . $this->db->_escape_identifiers($this->db->database . '.' . $table));
            // No result means the table name was invalid
            if ($query === FALSE) {
                continue;
            }
            // Write out the table schema
            $output .= '#' . $newline . '# TABLE STRUCTURE FOR: ' . $table . $newline . '#' . $newline . $newline;
            if ($add_drop === TRUE) {
                $output .= 'DROP TABLE IF EXISTS ' . $this->db->_protect_identifiers($table) . ';' . $newline . $newline;
            }
            $i = 0;
            $result = $query->result_array();
            foreach ($result[0] as $val) {
                if ($i++ % 2) {
                    $output .= $val . ';' . $newline . $newline;
                }
            }
            // If inserts are not needed we're done...
            if ($add_insert === FALSE) {
                continue;
            }
            // Grab all the data from the current table
            $query = $this->db->query('SELECT * FROM ' . $this->db->_protect_identifiers($table));
            if ($query->num_rows() === 0) {
                continue;
            }
            // Fetch the field names and determine if the field is an
            // integer type. We use this info to decide whether to
            // surround the data with quotes or not
            $i = 0;
            $field_str = '';
            $is_int = array();
            while ($field = $query->result_id->fetch_field()) {
                // Most versions of MySQL store timestamp as a string
                $is_int[$i] = in_array(strtolower($field->type), array('tinyint', 'smallint', 'mediumint', 'int', 'bigint'), //, 'timestamp'),
                        TRUE);
                // Create a string of field names
                $field_str .= $this->db->_escape_identifiers($field->name) . ', ';
                $i++;
            }
            // Trim off the end comma
            $field_str = preg_replace('/, $/', '', $field_str);
            // Build the insert string
            foreach ($query->result_array() as $row) {
                $val_str = '';
                $i = 0;
                foreach ($row as $v) {
                    // Is the value NULL?
                    if ($v === NULL) {
                        $val_str .= 'NULL';
                    } else {
                        // Escape the data if it's not an integer
                        $val_str .= ($is_int[$i] === FALSE) ? $this->db->escape($v) : $v;
                    }
                    // Append a comma
                    $val_str .= ', ';
                    $i++;
                }
                // Remove the comma at the end of the string
                $val_str = preg_replace('/, $/', '', $val_str);
                // Build the INSERT string
                $output .= 'INSERT INTO ' . $this->db->_protect_identifiers($table) . ' (' . $field_str . ') VALUES (' . $val_str . ');' . $newline;
            }
            $output .= $newline . $newline;
        }

        return $output;
    }

}
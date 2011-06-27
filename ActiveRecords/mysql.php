<?php
  /**
  * Ignited Datatables ActiveRecords library for MySql
  *
  * @subpackage libraries
  * @category   library
  * @version    0.1
  * @author     Yusuf Ozdemir <yusuf@ozdemir.be>
  */
  class ActiveRecords     
  {
    /**
    * Variables
    *
    */
    var $ar_select      = array();
    var $ar_from        = array();
    var $ar_join        = array();
    var $ar_where       = array();
    var $ar_orderby     = array();
    var $ar_limit       = FALSE;
    var $ar_offset      = FALSE;
    var $ar_order       = FALSE;
    
    var $_escape_char   = '`';
    var $_count_string  = 'SELECT COUNT(*) AS ';

    var $username       = 'root';
    var $password       = '';
    var $database       = '';
    var $hostname       = 'localhost';
    var $port           = '';
    var $db ;

    /**
    * Construct function
    *
    */
    function connect($config)
    {
      foreach ($config as $key => $val)
        if(in_array($key, array('hostname', 'username', 'password', 'database', 'port')))
          $this->$key = $val;
      $this->db_connect();
      $this->db_select();
    }

    /**
    * DB connection
    *
    */
    function db_connect()
    {
      if ($this->port != '')
        $this->hostname .= ':'.$this->port;
      $this->db = @mysql_connect($this->hostname, $this->username, $this->password, TRUE);
    }

    /**
    * DB Select
    *
    */
    function db_select()
    {
      @mysql_select_db( $this->database, $this->db ) or 
        die( 'Could not select database '. $this->database );        
    }

    /**
    * SELECT
    *
    */
    function select($columns, $backtick_protect)
    {
      foreach ($columns as $column)
        $this->ar_select[] = trim($column);
      return $this;
    }

    /**
    * FROM
    *
    */
    function from($from)
    {
      $from = explode(',', $from);
      foreach ((array)$from as $f)
        $this->ar_from[] = $this->_escape_char . trim($f) . $this->_escape_char;
      return $this;    
    }

    /**
    * JOIN
    *
    */
    function join($table, $cond, $type = '')
    {
      if ($type != '')
      {
        $type = strtoupper(trim($type));
        $type = (!in_array($type, array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER')))? '':$type.' ' ;
      }
      $join = $type.'JOIN '.$table.' ON '.$cond;
      $this->ar_join[] = $join;
      return $this;
    }

    /**
    * WHERE
    * // TODO : Backtick protection
    */
    function where($key, $value = NULL, $escape = FALSE, $type = 'AND ')
    {
      $prefix = (count($this->ar_where) == 0) ? '' : $type;
      $this->ar_where[] = $prefix.$key;
      return $this;
    }

    /**
    * LIMIT
    *
    */
    function limit($value, $offset = '')
    {
      $this->ar_limit = $value;
      if ($offset != '')
        $this->ar_offset = $offset;
      return $this;
    }

    /**
    * Offset
    *
    */
    function offset($offset)
    {
      $this->ar_offset = $offset;
      return $this;
    }

    /**
    * ORDERBY
    *
    */
    function order_by($orderby, $direction = '')
    {
      $direction = (in_array(strtoupper(trim($direction)), array('ASC', 'DESC'), TRUE)) ? ' '.$direction : ' ASC';
      $this->ar_orderby[] = $orderby.$direction;
      return $this;
    }

    /**
    * Run Query
    *
    */
    function get()
    {
      $aData = array();
      $result = mysql_query($this->_compile_select(), $this->db) or die(mysql_error());
      $this->_reset_select();
      while ( $aRow = mysql_fetch_array($result, MYSQL_ASSOC ) )
        $aData[] = $aRow;
      return $aData;
    }

    /**
    * Count Results
    *
    */
    function count_all_results($table = '')
    {    
      if ($table != '')
        $this->from($table);
      $sql = $this->_compile_select($this->_count_string . 'numrows');
      $query = mysql_query($sql) or die(mysql_error());

      if (mysql_num_rows($query) == 0)
        return 0;

      $this->_reset_select();
      $row = mysql_fetch_array($query);
      return (int) $row['numrows'];
    }

    /**
    * Compile sql string
    *
    */
    function _compile_select($q = NULL)
    {
      $sql  = ($q == NULL) ? 'SELECT ' : $q ;
      $sql .= implode(',', $this->ar_select);

      if(count($this->ar_from) > 0) 
        $sql .= "\nFROM (".implode(',', $this->ar_from).")";

      if (count($this->ar_join) > 0)
        $sql .= "\n".implode("\n", $this->ar_join);

      if (count($this->ar_where) > 0)
        $sql .= "\nWHERE " . implode(" ", $this->ar_where);

      if (count($this->ar_orderby) > 0)// check
      {
        $sql .= "\nORDER BY " . implode(', ', $this->ar_orderby);
        if ($this->ar_order !== FALSE)
          $sql .= ($this->ar_order == 'desc') ? ' DESC' : ' ASC';
      }

      if (is_numeric($this->ar_limit))
        $sql .= "\nLIMIT ".(($this->ar_offset == 0)? '' : $this->ar_offset.', ').$this->ar_limit;

      return $sql;
    }

    /**
    * Reset arrays
    *
    */
    function _reset_select()
    {
      $ar_reset_items = array(
        'ar_select'     => array(),
        'ar_from'       => array(),
        'ar_join'       => array(),
        'ar_where'      => array(),
        'ar_orderby'    => array(),
        'ar_limit'      => FALSE,
        'ar_offset'     => FALSE,
        'ar_order'      => FALSE    
       );
      foreach ($ar_reset_items as $item => $default_value)
        $this->$item = $default_value;
    }
  }
/* End of file ActiveRecords.php */
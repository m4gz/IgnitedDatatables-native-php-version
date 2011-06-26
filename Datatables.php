<?php
  /**
  * Ignited Datatables
  *
  * This is a wrapper class/library based on the native Datatables server-side implementation by Allan Jardine
  * found at http://datatables.net/examples/data_sources/server_side.html for CodeIgniter
  *
  * @package    CodeIgniter
  * @subpackage libraries
  * @category   library
  * @version    0.5.2 ( Vanilla PHP Version )
  * @author     Vincent Bambico <metal.conspiracy@gmail.com>
  *             Yusuf Ozdemir <yusuf@ozdemir.be>
  * @link       http://codeigniter.com/forums/viewthread/160896/
  */
  require_once('ActiveRecords.php');
  class Datatables
  {
    /**
    * Global container variables for chained argument results
    *
    */
    var $table;
    var $select         = array();
    var $joins          = array();
    var $columns        = array();
    var $where          = array();
    var $add_columns    = array();
    var $edit_columns   = array();
    var $unset_columns  = array();

    /**
    * Load ActiveRecord functions
    *
    */
    public function __construct() 
    {
      $this->ar = new ActiveRecords;
    }

    /**
    * Database settings
    *
    */
    public function connect($config) 
    {
      $this->ar->connect($config);
    }

    /**
    * Get input data (post or get)
    *
    */
    public function input($field) 
    {
      return mysql_real_escape_string((isset($_POST['sEcho']))? $_POST[$field] : $_GET[$field]) ;
    }

    /**
    * Generates the SELECT portion of the query
    *
    * @param string $columns
    * @param bool $backtick_protect
    * @return object
    */
    public function select($columns, $backtick_protect = TRUE)
    {
      foreach($this->explode(',', $columns) as $val)
      {
        $column = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$2', $val)); 
        $this->columns[] =  $column;
        $this->select[$column] =  trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$1', $val));
      }
      $this->ar->select($this->explode(',', $columns), $backtick_protect);
      
      return $this;
    }

    /**
    * Generates the FROM portion of the query
    *
    * @param string $table
    * @return string
    */
    public function from($table)
    {
      $this->table = $table;
      $this->ar->from($table);
      return $this;
    }

    /**
    * Generates the JOIN portion of the query
    *
    * @param string $table
    * @param string $fk
    * @param string $type
    * @return mixed
    */
    public function join($table, $fk, $type = NULL)
    {
      $this->joins[] = array($table, $fk , $type);
      $this->ar->join($table, $fk, $type);
      return $this;
    }

    /**
    * Generates the WHERE portion of the query
    *
    * @param mixed $key_condition
    * @param string $val
    * @param bool $backtick_protect
    * @return string
    */
    public function where($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
      $this->where[] = array($key_condition, $val, $backtick_protect);
      $this->ar->where($key_condition, $val, $backtick_protect);
      return $this;
    }

    /**
    * Sets additional column variables for adding custom columns
    *
    * @param string $column
    * @param string $content
    * @param string $match_replacement
    * @return mixed
    */
    public function add_column($column, $content, $match_replacement = NULL)
    {
      $match_replacement = $this->explode(',', $match_replacement);
      array_walk($match_replacement, create_function('&$val', '$val = trim($val);'));
      $this->add_columns[$column] = array('content' => $content, 'replacement' => $match_replacement);
      return $this;
    }

    /**
    * Sets additional column variables for editing columns
    *
    * @param string $column
    * @param string $content
    * @param string $match_replacement
    * @return mixed
    */
    public function edit_column($column, $content, $match_replacement)
    {
      $match_replacement = $this->explode(',', $match_replacement);
      array_walk($match_replacement, create_function('&$val', '$val = trim($val);'));
      $this->edit_columns[$column][] = array('content' => $content, 'replacement' => $match_replacement);
      return $this;
    }

    /**
    * Unset column
    *
    * @param string $column
    * @return object
    */
    public function unset_column($column)
    {
      $this->unset_columns[] = $column;
      return $this;
    }

    /**
    * Builds all the necessary query segments and performs the main query based on results set from chained statements
    *
    * @return string
    */
    public function generate()
    {
      $this->get_paging();
      $this->get_ordering();
      $this->get_filtering();
      return $this->produce_output();
    }

    /**
    * Generates the LIMIT portion of the query
    *
    * @return mixed
    */
    protected function get_paging()
    {
      $iStart = $this->input('iDisplayStart');
      $iLength = $this->input('iDisplayLength');
      $this->ar->limit(($iLength != '' && $iLength != '-1')? $iLength : 10, ($iStart)? $iStart : 0);
    }

    /**
    * Generates the ORDER BY portion of the query
    *
    * @return mixed
    */
    protected function get_ordering()
    {
      $sColArray = ($this->input('sColumns'))? explode(',', $this->input('sColumns')) : $this->columns;
      $columns = array_values(array_diff($this->columns, $this->unset_columns));
      for($i = 0; $i < intval($this->input('iSortingCols')); $i++)
        if(isset($sColArray[intval($this->input('iSortCol_' . $i))]) && in_array($sColArray[intval($this->input('iSortCol_' . $i))], $columns ))
          $this->ar->order_by($sColArray[intval($this->input('iSortCol_' . $i))], $this->input('sSortDir_' . $i));
    }

    /**
    * Generates the LIKE portion of the query
    *
    * @return mixed
    */
    protected function get_filtering()
    {
      $sWhere = '';
      $sSearch = mysql_real_escape_string($this->input('sSearch'));
      $columns = array_values(array_diff($this->columns, $this->unset_columns));
      $sColArray = ($this->input('sColumns'))? explode(',', $this->input('sColumns')) : $columns;

      if($sSearch != '')
        for($i = 0; $i < count($sColArray); $i++)
          if($this->input('bSearchable_' . $i) == 'true' && in_array($sColArray[$i], $columns))
            $sWhere .= $this->select[$sColArray[$i]] . " LIKE '%" . $sSearch . "%' OR ";

      $sWhere = substr_replace($sWhere, '', -3);

      if($sWhere != '')
        $this->ar->where('(' . $sWhere .')');
    }

    /**
    * Compiles the select statement based on the other functions called and runs the query
    *
    * @return mixed
    */
    protected function get_display_result()
    {
      return $this->ar->get();
    }

    /**
    * Builds a JSON encoded string data
    *
    * @return string
    */
    protected function produce_output()
    {
      $aaData = array();
      $rResult = $this->get_display_result();
      $iTotal = $this->get_total_results();
      $iFilteredTotal = $this->get_total_results(TRUE);

      foreach($rResult as $row_key => $row_val)
      {
        foreach($row_val as $field => $val)
          $aaData[$row_key][] = $val;

        foreach($this->add_columns as $add_val)
          $aaData[$row_key][] = $this->exec_replace($add_val, $aaData[$row_key]);

        foreach($this->edit_columns as $modkey => $modval)
          foreach($modval as $val)
            $aaData[$row_key][array_search($modkey, $this->columns)] = $this->exec_replace($val, $aaData[$row_key]);

        foreach($this->unset_columns as $column)
          if (in_array($column, $this->columns))
            unset($aaData[$row_key][array_search($column, $this->columns)]);
        $aaData[$row_key] = array_values($aaData[$row_key]);
      }

      $sColumns = $this->columns;
      foreach($this->unset_columns as $column)
        if(in_array($column, $this->columns))
          unset($sColumns[array_search($column, $this->columns)]);
      foreach($this->add_columns as $add_key => $add_val)
        $sColumns[] = $add_key;

      $sOutput = array
      (
        'sEcho'                => intval($this->input('sEcho')),
        'iTotalRecords'        => $iTotal,
        'iTotalDisplayRecords' => $iFilteredTotal,
        'aaData'               => $aaData,
        'sColumns'             => implode(',', $sColumns)
      );

      return json_encode($sOutput);
    }

    /**
    * Get result count
    *
    * @return integer
    */
    protected function get_total_results($filtering = FALSE)
    {
      if($filtering)
        $this->get_filtering();

      foreach($this->joins as $val)
        $this->ar->join($val[0], $val[1], $val[2]);

      foreach($this->where as $val)
        $this->ar->where($val[0], $val[1], $val[2]);

      return $this->ar->count_all_results($this->table);
    }

    /**
    * Runs callback functions and makes replacements
    *
    * @param mixed $custom_val
    * @param mixed $row_data
    * @return string $custom_val['content']
    */
    protected function exec_replace($custom_val, $row_data)
    {
      $replace_string = '';

      if(isset($custom_val['replacement']) && is_array($custom_val['replacement']))
      {
        foreach($custom_val['replacement'] as $key => $val)
        {
          if(preg_match('/callback\_(\w+)\((.+)\)/i', $val, $matches))
          {
            $func = $matches[1];
            $args = preg_split('/(?<!\\\),+/', $matches[2]);
            array_walk($args, create_function('&$val', '$val = trim($val);'));
            array_walk($args, create_function('&$val', '$val = str_replace("\,", ",", $val);'));

            foreach($args as $args_key => $args_val)
              if(in_array($args_val, $this->columns))
                $args[$args_key] = $row_data[array_search($args_val, $this->columns)];

            $replace_string = call_user_func_array($func, $args);
          }
          elseif(in_array($val, $this->columns))
            $replace_string = $row_data[array_search($val, $this->columns)];
          else
            $replace_string = $val;

          $custom_val['content'] = str_ireplace('$' . ($key + 1), $replace_string, $custom_val['content']);
        }
      }

      return $custom_val['content'];
    }

    /**
    * Return the difference of open and close characters
    *
    * @param string $str
    * @param string $open
    * @param string $close
    * @return string $retval
    */
    protected function balanceChars($str, $open, $close) {
      $openCount = substr_count($str, $open);
      $closeCount = substr_count($str, $close);
      $retval = $openCount - $closeCount;
      return $retval;
    }

    /**
    * Explode, but ignore delimiter until closing characters are found
    *
    * @param string $delimiter
    * @param string $str
    * @param string $open
    * @param string $close
    * @return mixed $retval
    */
    protected function explode($delimiter, $str, $open='(', $close=')') {
      $retval = array();
      $hold = array();
      $balance = 0;
      $parts = explode($delimiter, $str);
      foreach ($parts as $part) {
        $hold[] = $part;
        $balance += $this->balanceChars($part, $open, $close);
        if ($balance < 1) {
          $retval[] = implode($delimiter, $hold);
          $hold = array();
          $balance = 0;
        }
      }
      if (count($hold) > 0) {
        $retval[] = implode($delimiter, $hold);
      }
      return $retval;
    }

  }
/* End of file Datatables.php */
<?php

class Anvil_SQLite {
	private $handle;
	private $db_file_name;
	private $config_db_name = "/db/my_db.sqlite";

	private $sel_cols = array();
	private $where_arr = array();

	private $result;

	function __construct($no_open=FALSE) {
		if($no_open===FALSE) {
			$this->open_db();
		}
	}
	
	function open_db() {
		$this->db_file_name = APP_ROOT.$this->config_db_name;
		$this->handle = new SQLite3($this->db_file_name);
	}

	function query($query, $escape=TRUE) {
		$do_query = ($escape===TRUE)?$this->handle->escapeString($query):$query;
		$this->result = $this->handle->query($do_query);
		return $this->result;
	}

	function select($colnames) {
		$colnames=(is_array($colnames)?$colnames:array($colnames));
		$this->sel_cols = $colnames;
	}

	function where(array $where_arr) {
		$this->where_arr = $where_arr;
	}

	function get($tablename) {
		// Build the 'SELECT' part of the query
		$select_q= "SELECT ";
		$num_sel_cols = count($this->sel_cols);
		if($num_sel_cols == 0) {
			$select_q.="* ";
		} else {
			foreach($this->sel_cols as $a_col) {
				$select_q.=$a_col;
				if(--$num_sel_cols > 0)
					$select_q.=", ";
			}
		}
		// Build the 'FROM' part of the query
		$from_q = "FROM ".$tablename." ";
		// Build the 'WHERE' part of the query
		$where_q = "";
		$num_where_arr = count($this->where_arr);
		if($num_where_arr > 0) {
			$where_q = "WHERE ";
			foreach($this->where_arr as $a_col => $a_where) {
				$where_q.=$a_col." = '".$a_where."' ";
				if(--$num_where_arr > 0)
					$where_q.="AND ";
			}
		}
		return $this->query($select_q.$from_q.$where_q);
	}

	function fetch_array($res=NULL) {
		$res=(isset($res)?$res:$this->result);
		$i = 0;
		while($resx = $res->fetchArray(SQLITE3_ASSOC)) {
			$ret_arr[] = $resx;
		}
		return $ret_arr;
	}

	function insert($tablename, array $val_arr) {
		$ins_q = "INSERT INTO ".$tablename;
		$num_cols = count($val_arr);
		if($num_cols <= 0) { return false; }

		$ins_col1 = "(";
		$ins_col2 = " VALUES (";
		foreach($val_arr as $col_n => $val) {
			$ins_col1 .= $col_n;
			$ins_col2 .= "\"".$val."\"";
			if(--$num_cols > 0) {
				$ins_col1 .= ", ";
				$ins_col2 .= ", ";
			}
		}
		$ins_col1 .= ")";
		$ins_col2 .= ")";
		return $this->query($ins_q.$ins_col1.$ins_col2);
	}
}

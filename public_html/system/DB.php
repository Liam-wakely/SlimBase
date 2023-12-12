<?php
declare(strict_types=1);

namespace System;

use PDO;

/**
 * PDO methods to replace previous mysql functions: inc/gd_db.inc.php
 */
class DB {

  private static $didInit = false;
  private static $error;
  public  static $pdo;

  private static function init () {
    if (!self::$didInit) {
      self::$didInit = true;

      if (!self::$pdo) {
  			$dsn      = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST . ';charset=UTF8';
  			$user     = DB_USER;
  			$password = DB_PASS;

  			try {
  				self::$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_PERSISTENT => true));
  			} catch (PDOException $e) {
  				self::$error = $e->getMessage();
  				die(self::$error);
  			}
  		}else{
  			self::$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
  		}
    }
  }

  public static function reset_db_connection (){
    if (self::$pdo) {
      self::$pdo = NULL;
    }
    self::$didInit = false;
  }
  // --------------------------------------------------------- //
	public static function prep_query($query){
    DB::init();
		return self::$pdo->prepare($query);
	}

	public static function table_exists($table_name){
    DB::init();
		$stmt = self::prep_query('SHOW TABLES LIKE ?');
		$stmt->execute(array(self::add_table_prefix($table_name)));
		return $stmt->rowCount() > 0;
	}


  public static function array_to_fields_and_values($array, &$fields, &$values){
    DB::init();
    $keys = array_keys($array);
    $fields = '`' . implode("` = ?, `", $keys) . "` = ?";
    $values = array_values($array);
    //return $sets;
  }

  public static function row_exists($table_name, $array){
    DB::init();
    $fields = '';
    $values = array();
    self::array_to_fields_and_values($array, $fields, $values);
    $stmt = self::prep_query('SELECT * FROM `' . self::add_table_prefix($table_name) . '` WHERE ' . $fields);
    $stmt->execute($values);
    return $stmt->rowCount() > 0;
  }

  public static function row_exists_special($query, $values){
    DB::init();
		if($values == null){
			$values = array();
		}else if(!is_array($values)){
			$values = array($values);
		}
    $stmt = self::prep_query($query);
    $stmt->execute($values);
    return $stmt->rowCount() > 0;
  }

	public static function execute($query, $values = null){
    DB::init();
		if($values == null){
			$values = array();
		}else if(!is_array($values)){
			$values = array($values);
		}
		$stmt = self::prep_query($query);

		$execute = $stmt->execute($values);

    // if ($query == "") {

      // $stmt->debugDumpParams();

    // }

		if (!$execute) {
			return false;
		}
		return $stmt;
	}

	public static function fetch($query, $values = null){
    DB::init();
		if($values == null){
			$values = array();
		}else if(!is_array($values)){
			$values = array($values);
		}
		$stmt = self::execute($query, $values);

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public static function fetchAll($query, $values = null, $key = null){
    DB::init();
		if($values == null){
			$values = array();
		}else if(!is_array($values)){
			$values = array($values);
		}

		$stmt = self::execute($query, $values);
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		// Allows the user to retrieve results using a
		// column from the results as a key for the array
		if($key != null && $results[0][$key]){
			$keyed_results = array();
			foreach($results as $result){
				$keyed_results[$result[$key]] = $result;
			}
			$results = $keyed_results;
		}
		return $results;
	}

	public static function lastInsertId(){
    DB::init();
		return self::$pdo->lastInsertId();
	}


  // Wills Additions
  public static function fieldToArray($obj_array = [], $field_key = 0){
    if (!$field_key) return [];

    $array = [];

    foreach($obj_array as $key => $value){
      $array[] = $value[$field_key];
    }

    return $array;
  }

  public static function format_date_time($dateTime, $v_bShowTime = TRUE){
    $dateValue = date("");
    if(is_numeric($dateTime) ){
      $dateValue = $dateTime >= 0 ? gd_roundNum($dateTime) : 0;
    }else{
      //removes text month
      $_iDtmReturn = strtotime(gd_cAbbrMonthNames($v_szDtm));
      $dateValue = (($_iDtmReturn === -1) or ($_iDtmReturn === FALSE)) ? (-1) : ($_iDtmReturn);
    }
    return ($v_bShowTime) ? (strftime("%Y-%m-%d %H:%M:%S", intval($dateValue))) : (strftime("%Y-%m-%d", intval($dateValue)));
  }

  public static function getSqlString($string){
    DB::init();
    return self::$pdo->quote($string);
  }

  public static function weirdExecute($query){
    DB::init();
    return self::execute($query);
  }

}

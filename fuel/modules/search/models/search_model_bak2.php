<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

require_once(FUEL_PATH.'models/base_module_model.php');

class Search_model extends Base_module_model {
	
	public $required = array('location', 'scope', 'title', 'content');
	public $record_class = 'Search_item';

	function __construct()
	{
		parent::__construct('search', SEARCH_FOLDER);
		
	}
	
	// http://stackoverflow.com/questions/406587/php-mysql-advanced-search-page/406602#406602
	// http://www.joedolson.com/Search-Engine-in-PHP-MySQL.php
	// http://www.evolt.org/Boolean_Fulltext_Searching_with_PHP_and_MySQL
	function find_by_keyword($q, $limit = NULL, $offset = NULL, $excerpt_limit = 200)
	{
		$full_text_fields = array('title', 'content');
		$full_text_indexed = implode($full_text_fields, ', ');
	
		$q = trim($q); // trim the right and left from whitespace
		$q = preg_replace("#([[:space:]]{2,})#",' ',$q); // remove multiple spaces
		$q = $this->db->escape($q);
		
		$CI =& get_instance();
		if (strtolower($this->fuel_search->config('query_type')) == 'match')
		{
			$q = trim($q); // trim the right and left from whitespace
			switch($mode)
			{
				case 'exact':
					$this->db->where("MATCH(".$full_text_indexed.") AGAINST ('\"{$q}\"' IN BOOLEAN MODE))");
					break;
				case 'without':
					$this->db->where('MATCH('.$full_text_indexed.') AGAINST (-'.$q.' IN BOOLEAN MODE)');
					break;
				case 'at_least_one'
					$this->db->where("MATCH(".$full_text_indexed.") AGAINST ({$q} IN BOOLEAN MODE)");
					break;
			 	default:
					$this->db->where("MATCH(".$full_text_indexed.") AGAINST ({$q} IN BOOLEAN MODE)");
			}
		
			$this->db->select('match ('.$full_text_indexed.') against ("'.$q.'")  AS relevance ', FALSE);

	  	//	$this->db->where('MATCH('.$full_text_indexed.') AGAINST ('.$q.' IN BOOLEAN MODE)');
			$this->db->order_by('relevance desc');
		}
		else
		{
			switch($mode)
			{
				case 'at_least_one':
					$words = explode(' ', $q);
					foreach($words as $w)
					{
						foreach($full_text_fields as $field)
						{
							$this->db->or_where('LOWER('.$field.') LIKE "%'.$q.'%"');
						}
					}
					break;
				case 'exact':
					foreach($full_text_fields as $field)
					{
						$this->db->or_where('LOWER('.$field.') LIKE "'.$q.'"');
					}
					break;
				case 'without':
					foreach($full_text_fields as $field)
					{
						$this->db->or_where('LOWER('.$field.') NOT LIKE "%'.$q.'%"');
					}
					break;
				default:
					foreach($full_text_fields as $field)
					{
						$this->db->or_where('LOWER('.$field.') LIKE "%'.$q.'%"');
					}
			}

			$this->db->order_by('date_added desc');
		}
		
		$this->db->select($this->_tables['search'].'.location');
		$this->db->select($this->_tables['search'].'.title');
		$this->db->select($this->_tables['search'].'.date_added');
		$this->db->select('SUBSTRING('.$this->_tables['search'].'.content, 1, '.$excerpt_limit.') AS content_excerpt', FALSE);
		$this->db->limit($limit);
		
		if (!empty($offset))
		{
			$this->db->offset($offset);
		}
		$results = $this->find_all();
		$this->debug_query();
		
		
		// if (strtolower($this->fuel_search->config('query_type')) == 'match')
		// {
		// 	$this->db->where("MATCH(title, content) AGAINST ('\"{$q}\"' IN BOOLEAN MODE)");
		// }
		// else
		// {
		// 	switch($mode)
		// 	{
		// 		case 'at_least_one':
		// 			$words = explode(' ', $q);
		// 			foreach($words as $w)
		// 			{
		// 				$this->db->or_where('LOWER(content) LIKE "%'.$q.'$"');
		// 			}
		// 			break;
		// 		case 'exact':
		// 			$this->db->where('LOWER(content) LIKE "'.$q.'"');
		// 			break;
		// 		case 'without':
		// 			$this->db->where('LOWER(content) NOT LIKE "%'.$q.'%"');
		// 			break;
		// 		default:
		// 			$this->db->where('LOWER(content) LIKE "%'.$q.'%"');
		// 	}
		// }
		// if (strtolower($this->fuel_search->config('query_type')) == 'match')
		// {
		// 	switch($mode)
		// 	{
		// 		case 'exact':
		// 			$this->db->where("MATCH(title, content) AGAINST ('\"{$q}\"' IN BOOLEAN MODE)");
		// 			break;
		// 		case 'without':
		// 			$this->db->where('MATCH(title, content) AGAINST (-'.$q.') IN BOOLEAN MODE');
		// 			break;
		// 		case 'at_least_one': default:
		// 			$this->db->where("MATCH(title, content) AGAINST ('{$q}' IN BOOLEAN MODE");
		// 	}
		// }
		// else
		// {
		// 	switch($mode)
		// 	{
		// 		case 'at_least_one':
		// 			$words = explode(' ', $q);
		// 			foreach($words as $w)
		// 			{
		// 				$this->db->or_where('LOWER(content) LIKE "%'.$q.'$"');
		// 			}
		// 			break;
		// 		case 'exact':
		// 			$this->db->where('LOWER(content) LIKE "'.$q.'"');
		// 			break;
		// 		case 'without':
		// 			$this->db->where('LOWER(content) NOT LIKE "%'.$q.'%"');
		// 			break;
		// 		default:
		// 			$this->db->where('LOWER(content) LIKE "%'.$q.'%"');
		// 	}
		// }
		//$this->db->select('location, title, date_added, SUBSTRING(content, 1, '.$excerpt_limit.') AS content_excerpt, match (title, content) against ("'.$this->db->escape($q).'")  AS relevance ', FALSE);
		// $this->db->limit($limit);
		// $this->db->offset($offset);
		// $results = $this->find_all();
		
		
		// $words = preg_split('#\s#', $q);
		// 
		// $sql_where = '';
		// $has_paren = FALSE;
		// foreach($words as $key => $word)
		// {
		// 	if ($word == '(')
		// 	{
		// 		$sql_where .= '(';
		// 		$has_paren = TRUE;
		// 	}
		// 	else if (preg_match('#[a-zA-Z0-9_]+#', $word))
		// 	{
		// 		$sql_where .= '(MATCH (title, content) AGAINST (\''.$word.'\') > 0 )';
		// 		echo next($words).'xxx';
		// 		$join = (next($words) == 'AND') ? ' AND ' : ' OR ';
		// 		$sql_where .= $join;
		// 		continue;
		// 	}
		// }
		
		// echo "<pre style=\"text-align: left;\">";
		// print_r($q);
		// echo "</pre>";
		
		// foreach($words as $key => $word)
		// {
		// 	if (strtoupper($word) == 'AND')
		// 	{
		// 		$words[$key] = ' ';
		// 	}
		// 	else if (strtoupper($word) == 'OR')
		// 	{
		// 		$words[$key] = ', ';
		// 	}
		// 	else if (strtoupper($word) == 'NOT')
		// 	{
		// 		$words[$key] = ' -';
		// 	}
		// 	// else
		// 	// {
		// 	// 	
		// 	// }
		// }
		
		// $words = implode('', $words); // implode to join all them back
		// $words = explode(' ', $words); // now explode again
		
		// $sql =  "SELECT location, title, content, \n"
		// 		.$this->boolean_sql_select(
		// 			$this->boolean_inclusive_atoms($q), 'title, content')." as relevance \n"
		// 		."FROM $this->table_name \n"
		// 		."WHERE \n"
		// 		.$this->boolean_sql_where($q,'title, content')." \n"
		// 		."HAVING relevance>0 \n"
		// 		."ORDER BY relevance DESC \n";
				
		//$sql = $this->boolean_sql_where($q);
		// echo "<pre style=\"text-align: left;\">";
		// print_r($sql);
		// echo "</pre>";
		
	//	$this->debug_query();
		return $results;
	}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *	:: boolean_mark_atoms($string) ::
	 * 	used to identify all word atoms; works using simple
	 *	string replacement process:
	 *    		1. strip whitespace
	 *    		2. apply an arbitrary function to subject words
	 *    		3. represent remaining characters as boolean operators:
	 *       		a. ' '[space] -> AND
	 *       		b. ','[comma] -> OR
	 *       		c. '-'[minus] -> NOT
	 *    		4. replace arbitrary function with actual sql syntax
	 *    		5. return sql string
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	function boolean_mark_atoms($string)
	{
		$result = trim($string);
		$result = preg_replace("#([[:space:]]{2,})#",' ',$result);

		/* convert normal boolean operators to shortened syntax */
		$result = str_ireplace(' not ', ' -', $result);
		$result = str_ireplace(' and ', ' ', $result);
		$result = str_ireplace(' or ', ',', $result);

		/* strip excessive whitespace */
		$result = str_replace('( ', '(', $result);
		$result = str_replace(' )', ')', $result);
		$result = str_replace(', ', ',', $result);
		$result = str_replace(' ,', ',', $result);
		$result = str_replace('- ', '-', $result);

		/* apply arbitrary function to all 'word' atoms */
		$result = preg_replace(
			"#([A-Za-z0-9]{1,}[A-Za-z0-9\.\_-]{0,})#",
			"foo[('$0')]bar",
			$result);

		/* strip empty or erroneous atoms */
		$result = str_replace("foo[('')]bar", '', $result);
		$result = str_replace("foo[('-')]bar", '-', $result);

		/* add needed space */
		$result = str_replace(')foo[(', ') foo[(', $result);
		$result = str_replace(')]bar(', ')]bar (', $result);

		/* dispatch ' ' to ' AND ' */
		$result = str_replace(' ', ' AND ', $result);

		/* dispatch ',' to ' OR ' */
		$result = str_replace(',', ' OR ', $result);

		/* dispatch '-' to ' NOT ' */
		$result = str_replace(' -', ' NOT ', $result);

		return $result;
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *	:: boolean_sql_select($string,$match) ::
	 *	function used to transform a boolean search string into a
	 *	mysql parseable fulltext sql string used to determine the
	 *	relevance of each record;
	 *	1. put all subject words into array
	 *	2. enumerate array elements into scoring sql syntax
	 *	3. return sql string
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	function  boolean_sql_select($string,$match = 'title,content'){
		/* build sql for determining score for each record */
		preg_match_all(
			"([A-Za-z0-9]{1,}[A-Za-z0-9\-\.\_]{0,})",
			$string,
			$result);
		$result = $result[0];
		$stringsum_long = '';
		for($cth=0;$cth<count($result);$cth++){
			if(strlen($result[$cth])>=4){
				$stringsum_long .=
					" $result[$cth] ";
			}else{
				$stringsum_a[] =
					' '.Search_model::boolean_sql_select_short($result[$cth],$match).' ';
			}
		}
		if(strlen($stringsum_long)>0){
				$stringsum_a[] = " match ($match) against ('$stringsum_long') ";
		}
		$stringsum = implode("+",$stringsum_a);
		return $stringsum;
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *	:: boolean_sql_where($string,$match) ::
	 * 	function used to transform identified atoms into mysql
	 *	parseable boolean fulltext sql string; allows for
	 *	nesting by letting the mysql boolean parser evaluate
	 *	grouped statements
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	function boolean_sql_where($string, $match = 'title, content')
	{
		$result = $this->boolean_mark_atoms($string);

		/* dispatch 'foo[(#)]bar to actual sql involving (#) */
		$result = preg_replace(
			"#foo\[\(\'([^\)]{4,})\'\)\]bar#",
			" match ($match) against ('$1')>0 ",
			$result);
		$result = preg_replace(
			"#foo\[\(\'([^\)]{1,3})\'\)\]bar#e",
			" '('.Search_model::boolean_sql_where_short(\"$1\",\"$match\").')' ",
			$result);

		return $result;
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *	:: boolean_sql_where_short($string,$match) ::
	 *	parses short words <4 chars into proper SQL: special adaptive
	 *	case to force return of records without using fulltext index
	 *	keep in mind that allowing this functionality may have serious
	 *	performance issues, especially with large datasets
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	function boolean_sql_where_short($string, $match = 'title, content')
	{
		$match_a = explode(',',$match);
		for($ith=0;$ith<count($match_a);$ith++){
			$like_a[$ith] = " $match_a[$ith] LIKE '%$string%' ";
		}
		$like = implode(" OR ",$like_a);

		return $like;
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *	:: boolean_sql_select_short($string,$match) ::
	 *	parses short words <4 chars into proper SQL: special adaptive
	 *	case to force 'scoring' of records without using fulltext index
	 *	keep in mind that allowing this functionality may have serious
	 *	performance issues, especially with large datasets
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	function boolean_sql_select_short($string, $match = 'title, content')
	{
		$match_a = explode(',',$match);
		$score_unit_weight = .2;
		for($ith=0;$ith<count($match_a);$ith++){
			$score_a[$ith] =
				" $score_unit_weight*(
				LENGTH($match_a[$ith]) -
				LENGTH(REPLACE(LOWER($match_a[$ith]),LOWER('$string'),'')))
				/LENGTH('$string') ";
		}
		$score = implode(" + ",$score_a);

		return $score;
	}


	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *	:: boolean_inclusive_atoms($string) ::
	 *	returns only inclusive atoms within boolean statement
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	function boolean_inclusive_atoms($string)
	{

		$result=trim($string);
		$result=preg_replace("/([[:space:]]{2,})/",' ',$result);

		/* convert normal boolean operators to shortened syntax */
		$result=preg_replace('# not #i',' -',$result);
		$result=preg_replace('# and #i',' ',$result);
		$result=preg_replace('# or #i',',',$result);

		/* drop unnecessary spaces */
		$result=str_replace(' ,',',',$result);
		$result=str_replace(', ',',',$result);
		$result=str_replace('- ','-',$result);

		/* strip exlusive atoms */
		$result=preg_replace(
			"(\-\([A-Za-z0-9]{1,}[A-Za-z0-9\-\.\_\,]{0,}\))",
			'',
			$result);
		$result=preg_replace(
			"(\-[A-Za-z0-9]{1,}[A-Za-z0-9\-\.\_]{0,})",
			'',
			$result);
		$result=str_replace('(',' ',$result);
		$result=str_replace(')',' ',$result);
		$result=str_replace(',',' ',$result);

		return $result;
	}


	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *	:: boolean_parsed_as($string) ::
	 *	returns the equivalent boolean statement in user readable form
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	function boolean_parsed_as($string){
		$result = boolean_mark_atoms($string);

		/* dispatch 'foo[(%)]bar' to empty string */
		$result=str_replace("foo[('","",$result);
		$result=str_replace("')]bar","",$result);

		return $result;
	}
	function find_by_location($location)
	{
		return $this->find_one(array('location' => $location));
	}
	
	function form_fields($values = array(), $related = array())
	{
		$fields = parent::form_fields($values, $related);
		$fields['content']['class'] = 'no_editor';
		return $fields;
	}
	
	
	
}

class Search_item_model extends Base_module_record {
	
	public $content_excerpt = '';
	
	function get_url()
	{
		return site_url($this->location);
	}
	
}

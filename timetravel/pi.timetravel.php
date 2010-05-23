<?php if (!defined('EXT')) exit('Invalid file request');

$plugin_info = array(
	'pi_name'			=> 'Timetravel',
	'pi_version'		=> '0.1',
	'pi_author'			=> 'Wouter Vervloet',
	'pi_author_url'		=> '',
	'pi_description'	=> 'An easy way to navigate through entries by day, month or year',
	'pi_usage'			=> Timetravel::usage()
);

/**
* Timetravel Plugin class
*
* @package		  timetravel-ee2_addon
* @version			0.4.5
* @author			  Wouter Vervloet <wouter@baseworks.nl>
* @license			http://creativecommons.org/licenses/by-sa/3.0/
*/
class Timetravel {

	/**
	* Plugin return data
	*
	* @var string
	*/
	var $return_data = '';

	/**
	* Tag data
	*
	* @var string
	*/
	var $tagdata = '';
	
	/**
	* TODO
	*
	* @var array
	*/
	var $periods = array();
	
	/**
	* TODO
	*
	* @var int
	*/
	var $oldest = 0;
	
	/**
	* TODO
	*
	* @var int
	*/
	var $older = 0;
	
	/**
	* TODO
	*
	* @var int
	*/
	var $current = 0;

	/**
	* TODO
	*
	* @var int
	*/
	var $newer = 0;
	
	/**
	* TODO
	*
	* @var int
	*/
	var $newest = 0;

  /**
	* A list of formats used by each different type of timetravel.
	*
	* @var array
	*/
	var $formats = array(
	  'day' => 'Ymd',
    'month' => 'Ym',
    'year' => 'Y'
	);
	
  /**
  * ...
  * @todo Add variable description
  * @var array
  */
  var $url_formats = array(
    'day' => 'Y/m/d',
    'month' => 'Y/m',
    'year' => 'Y'
  );

	/**
	* PHP4 Constructor
	*
	* @see	__construct()
	*/
	function Timetravel()
	{
		$this->__construct();
	}

	/**
	* PHP5 Constructor
	*
	* @param	string	$date
	* @return	string
	*/
	function __construct()
	{
    $this->EE =& get_instance();
	  
	  if($this->_is_single_entry_page()) {
	    return false;
	  }
		  
	  $this->tagdata = $this->EE->TMPL->tagdata;
    
    $this->by = $this->EE->TMPL->fetch_param('by') ? $this->EE->TMPL->fetch_param('by') : $this->by;

	  $this->_set_current_period();    
    $this->_build_query();
    $this->_parse_template();
    
    return $this->return_data;
 
	}
	// END constructor

  function _parse_template()
  {
    // Find a parse the {current} tag
    if (strpos($this->tagdata, LD.'current') !== FALSE && preg_match_all("/".LD."current\s+format=([\"\'])([^\\1]*?)\\1".RD."/", $this->tagdata, $matches))
    {				
    	for ($j = 0; $j < count($matches[0]); $j++)
    	{				
    		$tagdata = str_replace($matches[0][$j], $this->EE->localize->decode_date($matches[2][$j], $this->current), $this->tagdata);
    	}
    }
    $tagdata = str_replace(LD.'current'.RD, $this->current, $tagdata);


    foreach ($this->EE->TMPL->var_pair as $key => $val)
    {
      switch($key)
      {
        case 'oldest':
        case 'older':
        case 'newer':
        case 'newest':
          $time = $this->$key;
          break;
      }
      
      if($time === 0) {
        $tagdata = $this->EE->TMPL->delete_var_pairs($key, $key, $tagdata);
        continue;
      }
      
      $inner = $this->EE->TMPL->fetch_data_between_var_pairs($tagdata, $key);
      
      if (strpos($inner, 'path=') !== FALSE)
  		{
  		  $tp = new Timetravel_path();
  		  $tp->time = date($this->url_formats[$this->by], $time);
        $inner = preg_replace_callback("/".LD."\s*path=(.*?)".RD."/", array(&$tp, 'alter_path'), $inner);
  		}
  		
      $tagdata = preg_replace("/".LD.$key.RD."(.*?)".LD.'\/'.$key.RD."/s", $inner, $tagdata);
    }
        
    $this->return_data = $tagdata;
  }
  // END _parse_template

  
  function _build_query()
  {
    // Do something awesome
    $allowed_params = array('by', 'channel', 'author_id', 'category', 'category_group', 'entry_id_from', 'entry_id_to', 'group_id', 'show_expired', 'show_future_entries', 'status', 'start_on','stop_before', 'uncategorized_entries', 'username');
        
    foreach($this->EE->TMPL->tagparams as $param => $val)
    {
      if(!in_array($param, $allowed_params)) {
        unset($this->EE->TMPL->tagparams[$param]);
      }
    }
    
    $this->EE->TMPL->tagparams['dynamic'] = 'off';
  
		if ( ! class_exists('Channel'))
		{
			require PATH_MOD.'channel/mod.channel.php';
		}

		$C = new Channel;
		$C->build_sql_query();

    if ($C->sql == '')
    {
    	return $this->EE->TMPL->no_results();
    }

    $this->query = $this->EE->db->query($C->sql);

		if ($this->query->num_rows() == 0)
		{
			return $this->EE->TMPL->no_results();
		}
		
		foreach($this->query->result() as $entry)
		{
		  
		  $loc = $this->EE->localize->set_localized_time($entry->entry_date);
	    $day = intval(date('d', $loc));
	    $month = intval(date('m', $loc));
	    $year = intval(date('Y', $loc));
	    		  
		  switch($this->by)
		  {		    
		    default:
		    case 'day':
  		    $this->periods[] = mktime(0, 0, 0, $month, $day, $year);
  		    break;
		    case 'month':
  		    $this->periods[] = mktime(0, 0, 0, $month, 1, $year);
  		    break;
		    case 'year':
  		    $this->periods[] = mktime(0, 0, 0, 1, 1, $year);
  		    break;
		      
		  }
		}
		
		$this->periods = array_unique($this->periods);
    rsort($this->periods);
    
    $cnt = count($this->periods)-1;
        
    $currIndex = array_search($this->current, $this->periods);
    
    if($currIndex === false) {
      $this->periods[] = $this->current;
      $this->periods = array_unique($this->periods);
      rsort($this->periods);
      $currIndex = array_search($this->current, $this->periods);
    }
        
    $this->oldest = isset($this->periods[$cnt]) && $this->periods[$cnt] != $this->current ? $this->periods[$cnt] : 0;
    $this->older = isset($this->periods[$currIndex+1]) ? $this->periods[$currIndex+1] : 0;
    $this->newer = isset($this->periods[$currIndex-1]) ? $this->periods[$currIndex-1] : 0;
    $this->newest = isset($this->periods[0]) && $this->periods[0] != $this->current ? $this->periods[0] : 0;
		
  }
  // END _build_query
  
  /**
	* Checks if the currently viewed page is a single entry page
	*
	* @return	boolean
	*/
  function _is_single_entry_page()
  {
    
    $in = $this->EE->uri->query_string;

 	  $results = $this->EE->db->query("SELECT entry_id, url_title FROM exp_channel_titles WHERE entry_id = '$in' OR url_title = '$in'");
    
    return ($results->num_rows() > 0) ? true : false;    

  }
  // END _is_single_entry_page

  /**
	* Set the currently watched day from the URI
	* or the present day if no valid date has been set.
	*
	* @return	void
	*/
  function _set_current_period()
  {
     
    switch($this->by)
    {
      case 'day':
        preg_match('/(?P<year>\d{4})\/(?P<month>\d{2})\/(?P<day>\d{2})/', $this->EE->uri->query_string, $parts);
        if( isset($parts['year']) && isset($parts['month']) && isset($parts['day']) )
        {
          $this->current = mktime(0, 0, 0, $parts['month'], $parts['day'], $parts['year']);
        }
        break;
      case 'month':
        preg_match('/(?P<year>\d{4})\/(?P<month>\d{2})/', $this->EE->uri->query_string, $parts);
        if( isset($parts['year']) && isset($parts['month']))
        {
          $this->current = mktime(0, 0, 0, $parts['month'], 1, $parts['year']);
        }
        break;
      case 'year':
        preg_match('/(?P<year>\d{4})/', $this->EE->uri->query_string, $parts);
        if( isset($parts['year']))
        {
          $this->current = mktime(0, 0, 0, 1, 1, $parts['year']);
        }      
        break;
    } 
  }

	/**
	* Plugin Usage
	*
	* @return	string
	*/    
	function usage()
	{
		ob_start(); 
?>
		
  {exp:timetravel
    by='day'
    author_id='1'
    category='1'
    category_group='1'
    channel='default_site'
    entry_id_from='1'
    entry_id_to='20'
    group_id=''
    show_expired='yes'
    show_future='yes'
    status='open'
    start_on='2004-06-05 20:00'
    stop_before='2010-06-05 20:00'
    uncategorized_entries='no'
    username='name'
  }

  {oldest}<a href="{path='plugins/timetravel'}">&laquo;Oldest</a>{/oldest} 
  {older}<a href="{path='plugins/timetravel'}">&lsaquo;Older</a>{/older} 
  <strong>{current format='%F %j%S, %Y'}</strong>
  {newer}<a href="{path='plugins/timetravel'}">Newer&rsaquo;</a>{/newer} 
  {newest}<a href="{path='plugins/timetravel'}">Newest&raquo;</a>{/newest}

{/exp:timetravel}

If you are using Timetravel to wakl through years, you need to add year='{segment_n}' and dynamic='off' to your channel:entries tag.
		
<?php
		$buffer = ob_get_contents();
		ob_end_clean(); 

		return $buffer;
	}
	// END usage

}
// END CLASS

/**
* This object is used as a placeholder for the preg_replace_callback
* function that alters the path variable.
*/
class Timetravel_path
{
  var $time;
  
  function alter_path($matches) 
  {
    $src = str_replace(array('"', "'"), '', $matches[1]);
    
    $path = explode('/', $src);
    $time = explode('/', $this->time);
    
    $repl = implode('/', array_merge($path, $time));
    return str_replace($matches[1], "'".$repl."'", $matches[0]);
    
  }
}


/* End of file pi.timetravel.php */
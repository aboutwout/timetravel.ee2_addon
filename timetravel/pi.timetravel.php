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
	* A list of formats used by each different type of timetravel.
	*
	* @var array
	*/
	var $formats = array(
	  'day' => '%Y%m%d',
    'month' => '%Y%m',
    'year' => '%Y'
	);
	
  /**
  * ...
  * @todo Add variable description
  * @var array
  */
  var $url_formats = array(
    'day' => '%Y/%m/%d',
    'month' => '%Y/%m',
    'year' => '%Y'
  );


  // =====================
  // Plugin parameters
  // =====================
  /**
  * ...
  * @todo add description
  * @var array
  */
  var $author_id = array();

  /**
  * Period used to split the entries
  *
  * @var string
  */
  var $by = 'day';
  
  /**
  * ...
  * @todo add description
  * @var array
  */
  var $category = array();

  /**
  * ...
  * @todo add description
  * @var array
  */
  var $category_group = array();
  
  /**
  * ...
  * @todo add description
  * @var array
  */
  var $channel = array();

  /**
  * ...
  * @todo add description
  * @var false|int
  */
  var $entry_id_from = false;

  /**
  * ...
  * @todo add description
  * @var false|int
  */
  var $entry_id_to = false;

  /**
  * ...
  * @todo add description
  * @var array
  */
  var $group_id = array();

  /**
  * ...
  * @todo add description
  * @var bool
  */
  var $show_expired = false;

  /**
  * ...
  * @todo add description
  * @var bool
  */
  var $show_future_entries = false;

  /**
  * ...
  * @todo add description
  * @var array
  */
  var $status = array('open');

  /**
  * ...
  * @todo add description
  * @var false|int
  */
  var $start_on = false;

  /**
  * ...
  * @todo add description
  * @var false|int
  */
  var $stop_before = false;

  /**
  * ...
  * @todo add description
  * @var bool
  */
  var $uncategorized_entries = true;

  /**
  * ...
  * @todo add description
  * @var array
  */
  var $username = array();


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
	var $current;

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
    
    $this->_build_query();
    $this->_parse_template();
    
    return $this->return_data;
 
	}
	// END constructor

  function _parse_template()
  {
  
    if (strpos($this->tagdata, LD.'current') !== FALSE && preg_match_all("/".LD."current\s+format=([\"\'])([^\\1]*?)\\1".RD."/", $this->tagdata, $matches))
    {				
    	for ($j = 0; $j < count($matches[0]); $j++)
    	{				
    		$tagdata = str_replace($matches[0][$j], $this->EE->localize->decode_date($matches[2][$j], $this->EE->localize->now), $this->tagdata);	
    	}
    }

    $tagdata = str_replace(LD.'current'.RD, $this->EE->localize->now, $tagdata);
    
    
    
    foreach ($this->EE->TMPL->var_pair as $key => $val)
    {
      switch($key)
      {
        
        case 'oldest':
          
          
          
          break;
          
        case 'older':

          break;
          
        case 'newer':

          break;
          
        case 'newest':

          break;        
      }
    }
    
    $this->return_data = $tagdata;
  }
  // END _parse_template

  function _fetch_params()
  {
    // Do something awesome
  }
  // END _fetch_params

  
  function _build_query()
  {
    // Do something awesome
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
    channel='static'
    entry_id_from='1'
    entry_id_to='20'
    group_id=''
    show_expired='yes'
    show_future='yes'
    status='open'
    start_on='2004-06-05 20:00'
    stop_before='2010-06-05 20:00'
    uncategorized_entries='no'
    username='wouter'
  }

  {oldest}<a href="{path='plugins/timetravel'}">&laquo;Oldest</a>{/oldest} 
  {older}<a href="{path='plugins/timetravel'}">&lsaquo;Older</a>{/older} 
  <strong>{current format='%F %j%S, %Y'}</strong>
  {newer}<a href="{path='plugins/timetravel'}">Newer&rsaquo;</a>{/newer} 
  {newest}<a href="{path='plugins/timetravel'}">Newest&raquo;</a>{/newest}

    {/exp:timetravel}
		
<?php
		$buffer = ob_get_contents();
		ob_end_clean(); 

		return $buffer;
	}
	// END usage

}
// END CLASS

function debug($vars) {
  echo "<pre>";
  print_r($vars);
  echo "</pre>";
}


/* End of file pi.timetravel.php */
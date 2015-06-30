<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Entry expiration time setter
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		Dan Copeland - National Arts Centre / Centre national des Arts
 * @link		http://nac-cna.ca
 */

class Eeleventh_hour_ext {

	// Setup custom settings in this array.
	public $settings = array(
		'channel_id' => '58'
	);

	public $description		= 'Sets the expiration date\'s time to 11:59PM';
	public $docs_url		= '';
	public $name			= 'Eleventh hour';
	public $settings_exist	= 'n';
	public $version			= '1.0';

	public $entry_expiration_timestamp;
	public $entry_expiration_time = "";


	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->settings = $this->settings;

	}// ----------------------------------------------------------------------
	
	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 *
	 * @return void
	 */
	public function activate_extension()
	{
		
		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'entry_expiration_submit_end',
			'hook'		=> 'entry_submission_absolute_end',
			'settings'	=> serialize($this->settings),
			'version'	=> $this->version,
			'enabled'	=> 'y',
			'priority'  => 10,
		);

		ee()->db->insert('extensions', $data);			
		
	}	

	// ----------------------------------------------------------------------
	
	/**
	 * entry_expiration_submit_end
	 *
	 * @param $entry_id, $meta, $data
	 * @return nada, no need.
	 */
	public function entry_expiration_submit_end( $entry_id, $meta, $data )
	{
		if($data["channel_id"] == $this->settings["channel_id"]){

			// grab all the event's top-level info(last_date, end_date & end_time, most importantly)
			$this->entry_data = ee()->db->select("*")
			->from("channel_titles")
			->where( array("entry_id"=>$entry_id) )
			->get();

			if ($this->entry_data->num_rows() > 0)
			{
				foreach ($this->entry_data->result() as $row)
				{
					// ensure expiration time is not empty, then set to end-of-day
					$this->entry_expiration_time = ($row->expiration_date == '0' ? '0' : $row->expiration_date);

					if ($this->entry_expiration_time != "0"){
						$this->entry_expiration_time = strftime('%Y%m%d0259', $this->entry_expiration_time);
						$this->entry_expiration_time = DateTime::createFromFormat('YmdHi', $this->entry_expiration_time);
						$this->entry_expiration_timestamp = $this->entry_expiration_time->getTimestamp();
						$this->new_entry_data = ee()->db->update( 'channel_titles', array( 'expiration_date'  => $this->entry_expiration_timestamp ), array( "entry_id"=>$entry_id) );
					}
				} 
			}
		}
	}

	// ----------------------------------------------------------------------

	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return 	mixed	void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}

		ee()->db->where('class', __CLASS__);
		ee()->db->update('extensions', array('settings' => serialize($this->settings)));
		ee()->db->update('extensions', array('version' => $this->version));

	}	
	
	// ----------------------------------------------------------------------
}

/* End of file ext.eeleventh_hour.php */
/* Location: /system/expressionengine/third_party/eeleventh_hour/ext.eeleventh_hour.php */
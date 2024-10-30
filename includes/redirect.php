<?php
namespace KOW_Redirect;

class Redirect extends Helper
{
	/**
	 * redirect types allowed.
	 * Note: 301 & 302 are best practices
	 */
	const REDIRECT_TYPES = [
		301=>'301 - Moved Permanently',
		302=>'302 - Moved Temporarily',
		303=>'303 - Moved Temporarily, Drop Request Data',
		307=>'307 - Moved Temporarily, STRICT',
		308=>'308 - Moved Permanently, STRICT'
	];

	public $to_url;
	public $from_url;
	public $redir_type;
	public $override;
	public $disabled;
	public $id;

	public function __construct($to, $from, $type, $override = false, $disabled = false, $id = 0)
	{
		parent::__construct();

		$this->id = \intval($id);
		if ($this->id == 0) {
			$this->action = 'add';
		} else {
			$this->action = 'none';
		}

		$this->disabled = $disabled;
		$this->override = $override;

		$this->redir_type = \intval($type);
		if (!isset(self::REDIRECT_TYPES[$this->redir_type])) {
			$this->errors['redirect-type'] = 'Please choose a valid redirect type';
		}

		//TODO: regex match $to and $from to make sure they are valid
		$this->from_url = $from;
		$this->to_url = $to;
	}

	public function is_valid()
	{
		return !\count($this->errors)>0;
	}

	public function validate()
	{
		if (!$this->is_valid()) {
			return false;
		}
		//TODO: do relational validation here
		return $this->is_valid();
	}

	public function push()
	{
		//TODO: attempt to push to database
	}

	public function render()
	{
		?>
		<div class="super-pill">
				<div class="d-flex ml-auto my-auto mr-1 progress-btn">
					<?php 
					if ($this->disabled) {
						echo '<a href="#" class="flex-fill bg-success">Enable</a>';
					} else {
						echo '<a href="#" class="flex-fill bg-warning">Disable</a>';
					}
					?>
					<a href="#" class="flex-fill bg-primary">Edit</a>
					<a href="#" class="flex-fill bg-danger">Delete</a>
				</div>
				<div class="sub-pill border-0">
					<div>status</div>
					<?php 
					if ($this->disabled) {
						echo '<div class="bg-warning text-white">disabled</div>';
					} else {
						echo '<div class="bg-success text-white">enabled</div>';
					}
					?>
				</div>
				<div class="sub-pill">
					<div>type</div>
					<div><?php echo $this->redir_type; ?></div>
				</div>
				<div class="sub-pill">
					<div>to</div>
					<?php 
					if (strpos($this->to_url, ':') != false) { ?>
					<a href="<?php echo $this->to_url; ?>"><?php echo $this->to_url; ?></a>
					<?php } else { ?>
					<div class="pr-0"><?php echo $this->lite_cache_func('get_site_url',[]); ?></div>
					<a class="pl-0" href="<?php echo get_site_url('',$this->to_url); ?>"><?php echo $this->to_url; ?></a>
					<?php } ?>
				</div>
				<div class="sub-pill">
					<div>from</div>
					<div class="pr-0"><?php echo $this->lite_cache_func('get_site_url',[]); ?></div>
					<a class="pl-0" href="<?php echo get_site_url('',$this->from_url); ?>"><?php echo $this->from_url; ?></a>
				</div>
				<div class="sub-pill">
					<div class="border-0"><input type="checkbox" name="sel" id="pill-<?php echo $this->id; ?>" style="margin:0px!important;"></div>
					<div class="bg-light pr-0"></div>
				</div>
			</div>
		<?php
	}

	/**
	 * @param Helper $Helper
	 */
	public static function pull($Helper, $page_num = 1, $page_len = 10)
	{
		$query = 'SELECT `id`, `from_url`, `to_url`, `redir_type`, `created`, `is_disabled`, `overridden` FROM `#prefix_points`';
		$q_vars = [];

		$count_query = 'SELECT count(*) FROM `#prefix_points`';
		$c_vars = [];

		$query .= " LIMIT %d,%d";
		$q_vars[] = ($page_num-1)*$page_len;
		$q_vars[] = $page_len;

		$res = $Helper->gettable($query, $q_vars);
		$count = \intval($Helper->getvar($count_query, $c_vars));

		$redirects = [];
		foreach ($res as $_k => $redirect) {
			$redirects[] = new Redirect($redirect->to_url, $redirect->from_url, $redirect->redir_type, $redirect->overridden == '1', $redirect->is_disabled == '1', $redirect->id);
		}
		return [$redirects, $count];
	}
}

<?php

namespace KOW_Redirect;

require_once(KOW_REDIRECT_ROOT.'/includes/database.php');

class Helper extends database
{
    /**
     * Generally front-end errors to show users why things are broken.
     */
    public $errors = [];
    
    /**
     * Page popups to show to the user (may include errors)
     */
    public $popups = [];

    /**
     * User settings which are defined in the settings page
     * Note: Defaults are defined here
     */
    public $settings = [
        'disable_yoast_redirects' => false,
        'drop_tables_on_uninstall' => false
    ];
    
    public $page = '';
    public $action = '';

    public static $prefix = 'kow_redirect_';
    public static $_settings;

    /**
     * Used for data that needs to be accessed many times per request.
     * !WARNING! lite cache lasts for only the duration of the request.
     */
    public static $lite_cache = [];

    public function __construct()
    {
        $this->loadUserSettings();
    }

    private function loadUserSettings()
    {
        if (Helper::$_settings == null) {
			$_settings = $this->cache_get('settings', date('U'));
			if ($_settings != false) {
				$this->settings = $_settings;
			}
			Helper::$_settings = $this->settings;
        } else {
            $this->settings = Helper::$_settings;
		}
    }
    
    public function saveUserSettings()
    {
		Helper::$_settings = $this->settings;
		$success = true;
		$this->cache_set('settings', $this->settings);
		return $success;
    }

    public function lite_cache_func($func, $args, $overwrite = false)
    {
        $fname = $func;
        if (\is_array($fname)) {
            $fname = $fname[1];
        }
        $md5 = \md5(\serialize([$fname, $args]));
        if (!isset(self::$lite_cache[$md5]) || $overwrite) {
            self::$lite_cache[$md5] = $func(...$args);
        }
        return self::$lite_cache[$md5];
    }
    
    public static function lite_store($key, $value)
    {
        self::$lite_cache[$key] = $value;
    }

    public static function lite_retrieve($key, $default = [])
    {
        if (isset(self::$lite_cache[$key])) {
            return self::$lite_cache[$key];
        } else {
            return $default;
        }
    }

    /* dylan wenzlau File based cache */
    public function cache_set($key, $val)
    {
        $val = \var_export($val, true);
        $val = \str_replace('stdClass::__set_state', '(object)', $val);
        // Write to temp file first to ensure atomicity
        $tmp = KOW_REDIRECT_ROOT . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR  . "$key." . \uniqid('', true) . '.tmp';
        \file_put_contents($tmp, '<?php $val = ' . $val . ';', LOCK_EX);
        \rename($tmp, KOW_REDIRECT_ROOT . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR  . $key . '.php');
    }
	
	/**
	 * $expire_seconds of -1 is to never expire
	 */
    public function cache_get($key, $expire_seconds = 3600)
    {
        $file = KOW_REDIRECT_ROOT . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR  . $key . '.php';
    
        if (file_exists($file)) {
            $file_timestamp = filemtime($file);
    
            if ($file_timestamp != false) {
                $mdate = date("U", filemtime($file));
                $date  = date("U");
                if ($expire_seconds != -1 && ($mdate + $expire_seconds) < $date) {
                    unlink($file);
                }
            }
        }
    
        if ((@include $file) === false) {
            return false;
        } else {
            return isset($val) ? $val : false;
        }
    }
    
    public function cache_remove($key)
    {
        $file = KOW_REDIRECT_ROOT . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR  . $key;
        unlink($file);
        return true;
    }

    public function store($value)
    {
        Helper::lite_store($this->page . \ucfirst($this->action), $value);
    }

    public function retrieve()
    {
        return Helper::lite_retrieve($this->page . \ucfirst($this->action));
    }

    public function is_site_admin()
    {
        return in_array('administrator', wp_get_current_user()->roles);
    }
    
    public function ajax_page()
    {
        //\var_dump($_POST);
        //die;
        $this->admin_page();
    }

    public function admin_page()
    {
        $page_name = $this->removePrefix($_REQUEST['page'], Helper::$prefix);
        $this->includePage($page_name, 'controller');
        
        if (class_exists('KOW_Redirect\page', false)) {
            global $PageObject;
            $PageObject = new page();
            $callable = [$PageObject, 'action'. \ucfirst($PageObject->action)];
            if (\is_callable($callable)) {
                $callable();
            }
            $this->includePage($PageObject->page, $PageObject->action);
        }
    }

    public function includePage($admin_page, $action)
    {
        $file = KOW_REDIRECT_ROOT.'/pages/'.$admin_page.'/'.$action.'.php';
        if (!\file_exists($file)) {
            $file = KOW_REDIRECT_ROOT.'/pages/404/index.php';
        }
        require($file);
    }

    public function containsPrefix($string, $query)
    {
        return substr($string, 0, strlen($query)) === $query;
    }

    public function removePrefix($str, $prefix)
    {
        if (substr($str, 0, strlen($prefix)) == $prefix) {
            return substr($str, strlen($prefix));
        }

        // func failed
        return $str;
    }

    public function get_file_url($file = __FILE__)
    {
        $_file = str_replace("/", "/", $file);
        $_content_dir = str_replace("/", "/", WP_CONTENT_DIR);
        $file_path = str_replace($_content_dir, "", $_file);
        if ($file_path) {
            return content_url($file_path);
        }
        return false;
    }
    
    public function create_input_check($name, $label, $checked = false)
    {
        $iserror = isset($this->errors[$name]); ?>
		<div class="form-check">
			<input class="form-check-input <?php echo $iserror?'is-invalid':''; ?>" type="checkbox" value="" id="<?php echo $name; ?>" <?php echo $checked?'checked':''; ?>>
			<label class="form-check-label" for="<?php echo $name; ?>"><?php echo $label; ?></label>
			<div class="invalid-feedback" id="<?php echo $name; ?>-inv"><?php echo $this->errors[$name]??''; ?></div>
		</div>
		<?php
    }

    public function create_input_dropdown($name, $label, $options, $selected = '')
    {
        $iserror = isset($this->errors[$name]); ?>
<label for="<?php echo $name; ?>"><?php echo $label; ?></label>
<select class="custom-select <?php echo $iserror?'is-invalid':''; ?>" id="<?php echo $name; ?>" required>
	<?php
if ($selected == '') { ?>
	<option selected disabled value="">Choose...</option>
		<?php }
        foreach ($options as $option) { ?>
			<option value="<?php echo $option[0]; ?>" <?php echo $option[0] == $selected?'selected':''; ?>><?php echo $option[1]; ?></option>
		<?php } ?>
</select>
<div class="invalid-feedback" id="<?php echo $name; ?>-inv"><?php echo $this->errors[$name]??''; ?></div>
<?php
    }
    
    public function create_input_text($name, $label, $required = false)
    {
        $iserror = isset($this->errors[$name]); ?>
<label for="<?php echo $name; ?>"><?php echo $label; ?></label>
<input type="text" class="form-control <?php echo $iserror?'is-invalid':''; ?>" id="<?php echo $name; ?>" value=""
	<?php echo $required?'required':''; ?>>
<div class="invalid-feedback" id="<?php echo $name; ?>-inv"><?php echo $this->errors[$name]??''; ?></div>
<?php
    }
    
    public function create_input_radio($name, $label, $options, $selected = '')
    {
        $iserror = isset($this->errors[$name]); ?>
<h6 class="card-subtitle mb-2 text-muted"><?php echo $label; ?></h6>
<?php
        $options_length = \count($options)-1;
        foreach ($options as $k => $option) { ?>
<div class="form-check">
	<input class="form-check-input <?php echo $iserror?'is-invalid':''; ?>" type="radio" name="<?php echo $name ?>"
		id="<?php echo $name; ?>-<?php echo $option[0]; ?>" value="<?php echo $option[0]; ?>"
		<?php echo $option[0] == $selected?'checked':''; ?>>
	<label class="form-check-label"
		for="<?php echo $name; ?>-<?php echo $option[0]; ?>"><?php echo $option[1]; ?></label>
	<?php
        if ($iserror && $k == $options_length) { ?>
	<div class="invalid-feedback"><?php echo $this->errors[$name]; ?></div>
	<?php } ?>
</div>
<?php }
        if ($iserror) {
            //* count error as handled
            unset($this->errors[$name]);
        }
    }

    public function generate_js()
    {
        if (isset(self::$lite_cache['js_generated'])) {
            return;
        }
        self::$lite_cache['js_generated'] = true; ?>
<script>
jQuery(document).ready(function($) {
	jQuery('.toast').toast({
		autohide: false
	});
	jQuery('.toast').toast('show');
});
</script>
<?php
    }
    
    public function create_toasts($show_errors = true)
    {
        if ($show_errors) {
            foreach ($this->errors as $key => $value) {
                $this->popups[] = [
                    $key,
                    $value,
                    'border-danger text-danger'
                ];
            }
        } ?>
<div class="toast-li">
	<?php

        foreach ($this->popups as $popup) { ?>
	<div class="toast <?php echo $popup[2]; ?>" role="alert" aria-live="assertive" aria-atomic="true">
		<div class="toast-header">
			<strong class="mr-auto"><?php echo $popup[0] ?></strong>
			<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="toast-body"><?php echo $popup[1] ?></div>
	</div>
	<?php } ?>
</div>

<?php
        $this->generate_js();
    }
}

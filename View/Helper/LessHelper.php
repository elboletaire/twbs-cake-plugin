<?php
/**
 * Simple helper for using lessc with cakephp
 * @author Òscar Casajuana <elboletaire@underave.net>
 * @version 1.0
 */

App::uses('AppHelper', 'View/Helper');

class LessHelper extends AppHelper
{
	public $helpers = array('Html', 'Javascript');

/**
 * Default lesscss options. 
 * The rootpath is automatically set depending on the env variable
 * 
 * @var array
 */
	public $less_options = array(
		'env'      => 'production'
	);

/**
 * The resulting options array from merging default and user values (on setOptions)
 * 
 * @var array
 */
	private $options = array();

/**
 * Stores the compilation error, in case it occurs
 * 
 * @var boolean
 */
	public $error = false;

/**
 * The lessphp compiler instance
 * 
 * @var lessc
 */
	private $Lessc; // the less compiler instance

/**
 * The css path name, where the output files will be stored
 * 
 * @var string
 */
	private $css_path  = 'css';

/**
 * The lesscss path name, where all original .less files reside
 * 
 * @var string
 */
	private $less_path = 'less';

/**
 * Initializes Lessc and cleans less and css paths
 */
	public function __construct()
	{
		App::import('Vendor', 'lessphp/lessc', array('file' => 'lessphp' . DS . 'lessc.inc.php'));
		$this->Lessc = new lessc();

		$this->less_path = trim($this->less_path, '/');
		$this->css_path  = trim($this->css_path, '/');
	}

/**
 * Compile the less and return a css <link> tag.
 * In case of error, it will load less with  javascript 
 * instead of returning the resulting css <link> tag.
 * 
 * @param  string $less The input .less file to be compiled
 * @param  array  $options An array of options to be passed as a json to the less javascript object.
 * @return string The resulting <link> tag for the compiled css, or the <link> tag for the .less & less.min if compilation fails
 * @throws Exception
 */
	public function less($less = 'styles.less', array $options = array())
	{
		$options = $this->setOptions($less, $options);

		$css = $options['output'];
		$this->cleanOptions($options);

		if ($options['env'] == 'development')
		{
			return $this->jsBlock($less, $options);
		}

		try
		{
			$this->compile($less, $css);
			return $this->Html->css($css);
		}
		catch (Exception $e)
		{
			$this->error = $e->getMessage();

			$this->log("Error compiling less file: " . $this->error, 'less');
			// maybe here we should also add a trigger_error, but less.js should treat the error as we're gonna load it now
			return $this->jsBlock($less, $options);
		}
	}

/**
 * Returns the initialization string for less (javascript based)
 * 
 * @param  string $less The input .less file to be loaded
 * @param  array  $options An array of options to be passed to the `less` configuration var
 * @return string The link + script tags need to launch lesscss
 */
	public function jsBlock($less, array $options = array())
	{
		$options = $this->setOptions($less, $options);

		$lessjs = $options['less'];
		$this->cleanOptions($options);

		$return = sprintf('<link rel="stylesheet/less" href="%s" />', Router::url('/' . $this->less_path . '/' . $less));
		$return .= $this->Html->scriptBlock(sprintf('less = %s;', json_encode($options)));
		$return .= $this->Html->script($lessjs);

		return $return;
	}

/**
 * Compiles an input less file to an output css file using the PHP compiler
 * 
 * @param  string $input The input .less file to be compiled
 * @param  string $output The output .css file, resulting from the compilation
 * @return boolean true on success, false otherwise
 */
	public function compile($input, $output)
	{
		// load cache file
		$cache_file = CACHE . basename($input) . '.cache';
		$input      = WWW_ROOT . $this->less_path . DS . basename($input);
		$output     = WWW_ROOT . $this->css_path  . DS . basename($output);

		if (file_exists($cache_file)) {
			$cache = unserialize(file_get_contents($cache_file));
		} else {
			$cache = $input;
		}

		$new_cache = $this->Lessc->cachedCompile($cache);

		if (!is_array($cache) || $new_cache['updated'] > $cache['updated']) {
			if (false === file_put_contents($cache_file, serialize($new_cache))) {
				throw new Exception("Could not write less cache file to $cache_file");
			}
			if (false === file_put_contents($output, $new_cache['compiled'])) {
				throw new Exception("Could not write output css file to $output");
			}
			return true;
		}
		return false;
	}

/**
 * Sets the less configuration var options based on the ones given by the user
 * and our default ones.
 * 
 * @param string $less The input .less file to be processed
 * @param array  $options An array of options to be passed to the javascript less configuration var
 * @return array $options The resulting $options array
 */
	private function setOptions($less, array $options)
	{
		if (!empty($this->options)) {
			return $this->options;
		}

		$options = array_merge($this->less_options, $options);

		$options['rootpath'] = Router::url('/');

		if (empty($options['output'])) {
			$pathinfo = pathinfo($less);
			$options['output'] = $pathinfo['filename'] . '.css';
		}

		if (empty($options['less'])) {
			$options['less'] = 'less.min';
		}

		return $this->options = $options;
	}

	private function cleanOptions(array &$options)
	{
		unset($options['output'], $options['less']);
	}
}

<?php
namespace Bootstrap\View\Helper;

use Cake\View\View;
use Cake\Routing\Router;
use App\View\Helper\AppHelper;

/**
 * Simple helper for using lessc with cakephp
 * @author Òscar Casajuana <elboletaire@underave.net>
 * @version 1.2
 */
class LessHelper extends AppHelper
{
	public $helpers = array('Html');

/**
 * Default lesscss options.
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
	public function __construct(View $View, array $config = array())
	{
		parent::__construct($View, $config);

		require ROOT . DS . 'vendor' . DS . 'oyejorge' . DS . 'less.php' . DS . 'lib' . DS . 'Less' . DS . 'Autoloader.php';

		\Less_Autoloader::register();

		$this->Lessc = new \Less_Parser(array('compress' => true));

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
	public function less($less = 'styles.less', array $options = array(), array $modify_vars = array())
	{
		$options = $this->setOptions($options);

		$this->cleanOptions($options);

		if ($options['env'] == 'development')
		{
			return $this->jsBlock($less, $options);
		}

		try
		{
			$css = $this->compile($less, $modify_vars, $this->options['cache']);
			if (isset($this->options['tag']) && !$this->options['tag']) {
				return $css;
			}
			return $this->Html->css($css);
		}
		catch (Exception $e)
		{
			$this->error = $e->getMessage();

			$this->log("Error compiling less file: " . $this->error, 'less');

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

		$return = '';
		// Append the user less files
		foreach ($less as $les) {
			$this->Html->tag('metalink', Router::url('/' . $this->less_path . '/' . $les), array('rel' => 'stylesheet/less'));
		}
		// Less.js configuration
		$return .= $this->Html->scriptBlock(sprintf('less = %s;', json_encode($options)));
		// <script> tag for less.js file
		$return .= $this->Html->script($lessjs);
		// Set @bootstrap variable and reload .less files
		// $return .= $this->Html->scriptBlock('less.modifyVars({"bootstrap": \'"/Bootstrap/less/"\'}, true);');
		// Kown bug: throw of an "undefined variable @bootstrap" notice

		return $return;
	}

/**
 * Compiles an input less file to an output css file using the PHP compiler
 *
 * @param  string $input The input .less file to be compiled
 * @param  string $output The output .css file, resulting from the compilation
 * @return boolean true on success, false otherwise
 */
	public function compile($input, $modify_vars = array(), $cache = true)
	{
		if (!is_array($input)) {
			$input = array($input);
		}

		$to_parse = array();
		foreach ($input as $in) {
			$in = realpath(WWW_ROOT . $this->less_path . DS . $in);
			$to_parse[$in] = '';
		}

		if ($cache) {
			\Less_Cache::$cache_dir = WWW_ROOT . $this->css_path . DS;

			return \Less_Cache::Get($to_parse, array(), $modify_vars);
		}

		foreach ($to_parse as $file => $empty) {
			$this->Lessc->parseFile($file, '');
		}

		return $this->Lessc->getCss();
	}

/**
 * Sets the less configuration var options based on the ones given by the user
 * and our default ones.
 *
 * @param string $less The input .less file to be processed
 * @param array  $options An array of options to be passed to the javascript less configuration var
 * @return array $options The resulting $options array
 */
	private function setOptions(array $options)
	{
		$options = array_merge($this->less_options, $options);

		$options['rootpath'] = Router::url('/');

		if (empty($options['less'])) {
			$options['less'] = 'less.min';
		}

		if (!isset($options['cache'])) {
			$options['cache'] = true;
		}

		return $this->options = $options;
	}

	private function cleanOptions(array &$options)
	{
		unset($options['output'], $options['less'], $options['tag'], $options['cache']);
	}
}

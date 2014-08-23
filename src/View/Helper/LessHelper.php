<?php
namespace Bootstrap\View\Helper;

use Cake\Core\Plugin;
use Cake\Log\Log;
use Cake\View\View;
use Cake\Routing\Router;
use Cake\View\Helper;

/**
 * Simple helper for using lessc with cakephp
 * @author Òscar Casajuana <elboletaire@underave.net>
 * @version 3.0.0
 */
class LessHelper extends Helper
{
	public $helpers = [
		'Html'
	];

/**
 * Default lessjs options. Some are defined on setOptions due to the need of using methods.
 *
 * @var array
 */
	public $lessjs_defaults = [
		'env'      => 'production'
	];

/**
 * Default lessc options. Some are defined on setOptions due to the need of using methods.
 *
 * @var array
 */
	private $parser_defaults = [
		'compress' => true
	];

/**
 * Stores the compilation error, in case it occurs
 *
 * @var boolean
 */
	public $error = false;

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
	public function __construct(View $View, array $config = [])
	{
		parent::__construct($View, $config);

		// Initialize oyejorge/less.php parser
		require ROOT . DS . 'vendor' . DS . 'oyejorge' . DS . 'less.php' . DS . 'lib' . DS . 'Less' . DS . 'Autoloader.php';
		\Less_Autoloader::register();

		$this->less_path = trim($this->less_path, '/');
		$this->css_path  = trim($this->css_path, '/');

	}

/**
 * Compile the less and return a css <link> tag.
 * In case of error, it will load less with  javascript
 * instead of returning the resulting css <link> tag.
 *
 * @param  mixed $less The input .less file to be compiled or an array of .less files
 * @param  array $parser_options The options to be passed to the less php compiler
 * @param  array  $lessjs_options An array of options to be passed as a json to the less javascript object.
 * @return string The resulting <link> tag for the compiled css, or the <link> tag for the .less & less.min if compilation fails
 * @throws Exception
 */
	public function less($less = 'styles.less', array $options = [], array $modify_vars = [])
	{
		$options = $this->setOptions($options);
		$less = (array)$less;

		if ($options['js']['env'] == 'development')
		{
			return $this->jsBlock($less, $options);
		}

		try
		{
			$css = $this->compile($less, $options['parser'], $modify_vars, $options['cache']);
			if (isset($options['tag']) && !$options['tag']) {
				return $css;
			}
			if (!$options['cache']) {
				return $this->Html->formatTemplate('style', ['content' => $css]);
			}
			return $this->Html->css($css);
		}
		catch (Exception $e)
		{
			$this->error = $e->getMessage();

			Log::write('warning', "Error compiling less file: " . $this->error);

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
	public function jsBlock($less, array $options = [])
	{
		$return = '';
		// Append the user less files
		foreach ($less as $les) {
			$return .= $this->Html->meta('link', null, [
				'link' => Router::url('/' . $this->less_path . '/' . $les),
				'rel' => 'stylesheet/less'
			]);
		}
		// Less.js configuration
		$return .= $this->Html->scriptBlock(sprintf('less = %s;', json_encode($options['js'])));
		// <script> tag for less.js file
		$return .= $this->Html->script($options['less']);

		return $return;
	}

/**
 * Compiles an input less file to an output css file using the PHP compiler
 *
 * @param  string $input The input .less file to be compiled
 * @param  string $output The output .css file, resulting from the compilation
 * @return boolean true on success, false otherwise
 */
	public function compile($input, array $options = [], array $modify_vars = [], $cache = true)
	{
		$to_parse = [];
		foreach ($input as $in) {
			$in = realpath(WWW_ROOT . $this->less_path . DS . $in);
			$to_parse[$in] = '';
		}

		if ($cache) {
			\Less_Cache::$cache_dir = WWW_ROOT . $this->css_path . DS;

			return \Less_Cache::Get($to_parse, $options, $modify_vars);
		}

		$lessc = new \Less_Parser($options);
		$lessc->ModifyVars($modify_vars);

		foreach ($to_parse as $file => $empty) {
			$lessc->parseFile($file, '');
		}

		return $lessc->getCss();
	}

/**
 * Sets the less configuration var options based on the ones given by the user
 * and our default ones.
 *
 * @param array  $options An array of options containing our options combined with the ones for the parsers
 * @return array $options The resulting $options array
 */
	private function setOptions(array $options)
	{
		$this->parser_defaults = array_merge($this->parser_defaults, [
			'import_callback' => function($lessTree) {
				if ($path_and_uri = $lessTree->PathAndUri()) {
					return $path_and_uri;
				}
				$basefile = $lessTree->getPath();
				$basefile = substr($basefile, strpos($basefile, 'less/'), strlen($basefile));

				$bootstrap_file = Plugin::path('Bootstrap') . 'webroot' . DS . $basefile;
				if (file_exists($bootstrap_file)) {
					return [$bootstrap_file, dirname($basefile) . '/'];
				}

				return null;
			}
		]);
		$this->lessjs_defaults = array_merge($this->lessjs_defaults, [
			'rootpath' => Router::url('/')
		]);

		if (empty($options['parser'])) {
			$options['parser'] = $this->parser_defaults;
		} else {
			$options['parser'] = array_merge($this->parser_defaults, $options['parser']);
		}

		if (empty($options['js'])) {
			$options['js'] = $this->lessjs_defaults;
		} else {
			$options['js'] = array_merge($this->lessjs_defaults, $options['js']);
		}

		if (empty($options['less'])) {
			$options['less'] = '/bootstrap/js/less.min';
		}

		if (!isset($options['cache'])) {
			$options['cache'] = true;
		}

		return $options;
	}
}

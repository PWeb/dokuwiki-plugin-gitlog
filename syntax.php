<?php
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

// implode, explode delimiter
define('GITLOG_DELIMITER', '|');

class syntax_plugin_gitlog extends DokuWiki_Syntax_Plugin
{
	function getType()
	{
		return 'substition';
	}
 
	function getSort()
	{
		return 999;
	}
 
 	/**
 	 * Registers the regular expressions
 	 * @param  mixed $mode
 	 * @return void
 	 */
	function connectTo($mode)
	{
		$this->Lexer->addSpecialPattern('<gitlog:.+?>', $mode, 'plugin_gitlog');
	}
	
	/**
	 * Prepares the matched syntax for use in the renderer
	 * @param  mixed $match
	 * @param  mixed $state
	 * @param  mixed $pos
	 * @param  Doku_Handler $handler
	 * @return array
	 */
	function handle($match, $state, $pos, Doku_Handler $handler)
	{
		// default value
		$parameters = array();

		// regex
		preg_match_all('#(\w+)\s*=\s*"(.*?)"#', $match, $return);

		if (is_array($return) && isset($return[1]) && is_array($return[1]))
		foreach($return[1] as $index => $name)
		{
			$parameters[$name] = $return[2][$index];
		}

		return $parameters;
	}
 
 	/**
 	 * Renders Plugin Output
 	 * @param  string        $mode
 	 * @param  Doku_Renderer $renderer
 	 * @param  array         $data
 	 * @return bool
 	 */
	function render($mode, Doku_Renderer $renderer, $data)
	{
		if($mode == 'xhtml')
		{
			try {

				// check if repository is set
				if( ! isset($data['repository'])) {
					throw new Exception('no repository set', 1);
				}

				// check limit parameter
				if(isset($data['limit']) && is_numeric($data['limit'])) {
					$limit = (int)($data['limit']);
				} else {
					$limit = 10;
				}

				// check bare parameter
				if (empty($data['bare'])) {
					$bare=false;
				} else {
					$bare=true;
				}

				// if a dir parameter is set, use this instead of config value
				if ( ! empty($data['dir']) ) {
					$repository = $this->clean_git_dir($data['dir']).$data['repository'];
				} else {
					$repository = $this->clean_git_dir($this->getConf('root_dir')).$data['repository'];
				}

				// check if path is invalid
				if ( ! is_dir(dirname($repository)) ) {
					throw new Exception('repository path not valid >> '.$repository, 1);
				}
				
				// get the git log and changed files
				$log = $this->git_log($repository, $limit, $bare);

				// start rendering
				$renderer->doc .= '<ul class="gitlogplugin">';

				foreach($log as $row)
				{
					$renderer->doc .= '<li class="commit"><div class="message">';
					$renderer->doc .= hsc($row['message']);
					$renderer->doc .= '</div><div class="meta">';
					$renderer->doc .= hsc($row['author']).' : '.date($this->getConf('date_format'), $row['timestamp']);				
					$renderer->doc .= '</div>';

					// render changed file list if any
					if ( ! empty($row['changedfiles']) ) {

						$renderer->doc .= ' <a href="#" class="seechanges">[See Changes]</a>';

						$renderer->doc .= '<ul class="changedfiles">';
						foreach ($row['changedfiles'] as $changedfile) {
							$renderer->doc .= '<li>'.hsc($changedfile).'</li>';
						}
						$renderer->doc .= '</ul>';
					}

					$renderer->doc .= '</li>';
					
				}

				$renderer->doc .= '</ul>';

			} catch (Exception $e) {
				
				$renderer->doc .= 'Error: ' . $e->getMessage();
				return true;

			}

			return true;
		}

		return false;
	}

	/**
	 * Main Function to get log and changed files
	 * @param  string  $repo
	 * @param  integer $limit
	 * @param  boolean $bare
	 * @return array
	 */
	function git_log($repo, $limit = 10, $bare=false)
	{
		$format = array('%H', '%at', '%an', '%s');
		$params = implode(GITLOG_DELIMITER, $format);
		$data = $this->run_git('log --pretty=format:"'.$params.'" -'.$limit, $repo, $bare);	
		$result = array();

		foreach($data as $line)
		{
			// explode
			$columns = explode(GITLOG_DELIMITER, $line);

			// run git show command
			$changedfiles = $this->run_git('show --pretty="format:" --name-only '.$columns[0], $repo, $bare);

			$row = array(
				'commit' => $columns[0],
				'timestamp' => $columns[1],
				'author' => $columns[2],
				'message' => $columns[3],
				'changedfiles' => $this->cleanup_git_show($changedfiles),
			);

			$result[] = $row;
		}

		return $result;
	}

	/**
	 * Runs a git command
	 * @param  string  $command
	 * @param  string  $repo
	 * @param  boolean $bare
	 * @return mixed
	 */
	function run_git($command, $repo, $bare=false)
	{
		// if not bare, add git folder
		if ( ! $bare ) {
			$repo .= DIRECTORY_SEPARATOR . '.git';
		}
			
		$output = array();
		$ret = 0;
		$c = $this->getConf('git_exec').' --git-dir="'.$repo.'" '.$command;
		exec($c, $output, $ret);

		if ($ret != 0) {

			//an error

			$exceptionmessage = "The following command failed:<br>";
			$exceptionmessage .= $c . "<br>";
			$exceptionmessage .= "Please check configuration and/or path!";

			throw new Exception($exceptionmessage, 1);

		}

		return $output;
	}

	/**
	 * Removes empty elements from Array
	 * @param  array $input
	 * @return array
	 */
	function cleanup_git_show(Array $input)
	{
		return array_filter($input, array($this, 'remove_empty'));
	}

	/**
	 * Array filter, removes empty elements
	 * @param  mixed $value
	 * @return mixed
	 */
	function remove_empty($value)
	{
		return !empty($value) || $value === 0;
	}

	/**
	 * Cleans the git_dir string and
	 * removes possible errors
	 * @param  string $value
	 * @return string
	 */
	function clean_git_dir($value)
	{
		$value = trim($value, "'");
		return rtrim($value, "\/").DIRECTORY_SEPARATOR;
	}
}
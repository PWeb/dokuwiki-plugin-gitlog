<?php
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');


function find_end($string, $offset = 0)
{
	if($offset >= strlen($string))
		return FALSE;
	for($i = $offset; $i < strlen($string); $i++)
	{
		if(!is_numeric($string[$i]))
			return $i;
	}
	return strlen($string);
}

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
 
	function connectTo($mode)
	{
		$this->Lexer->addSpecialPattern('<gitlog:.+?>', $mode, 'plugin_gitlog');
	}
	
	function handle($match, $state, $pos, &$handler)
	{
		$start = strlen('<gitlog:');
		$end = -1;
		$params = substr($match, $start, $end);
		$params = preg_replace('/\s{2,}/', '', $params);
		$params = preg_replace('/\s[=]/', '=', $params);
		$params = preg_replace('/[=]\s/', '=', $params);

		$return = array();
		foreach(explode(' ', $params) as $param)
		{
			$val = explode('=', $param);
			$return[$val[0]] = $val[1];
		}
		return $return;
	}
 
 	/**
 	 * Renders Plugin Output
 	 * @param  string        $mode
 	 * @param  Doku_Renderer $renderer
 	 * @param  array         $data
 	 * @return bool
 	 */
	function render($mode, Doku_Renderer &$renderer, $data)
	{
		// check if settings are set
		if ( ! $this->getConf('root_dir') ) {
			return false;
		}

		$elements = array(
			'ft' => 'features',
			'bug' => 'bugs'
		);

		if($mode == 'xhtml')
		{
			// if no repository is set with parameter
			if(isset($data['repository']))
			{
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

				// get the git log and changed files
				$log = $this->git_log($data['repository'], $limit, $bare);

				// start rendering
				$renderer->doc .= '<ul class="gitlogplugin">';

				foreach($log as $row)
				{
					$renderer->doc .= '<li class="commit"><span class="message">';
					$renderer->doc .= hsc($row['message']);
					$renderer->doc .= '</span><span class="meta">';
					$renderer->doc .= hsc($row['author']).' : '.date($this->getConf('date_format'), $row['timestamp']);				
					$renderer->doc .= '</span>';

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
		$params = implode($this->getConf('delimiter'), $format);
		$data = $this->run_git('log --pretty=format:"'.$params.'" -'.$limit, $repo, $bare);	
		$result = array();

		foreach($data as $line)
		{
			// explode
			$columns = explode($this->getConf('delimiter'), $line);

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
		$repo = str_replace('/', '', $repo);
		$repo = str_replace('\\', '', $repo);

		if (!$bare) {
			$repo.='/.git';	
		}
			
		$output = array();
		$ret = 0;
		$c = $this->getConf('git_exec').' --git-dir='.$this->getConf('root_dir').$repo.' '.$command;
		exec($c, $output, $ret);

		if ($ret != 0) { 
			//debug
			echo($c);
			die('git failed, is git path correct?'); 
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
}
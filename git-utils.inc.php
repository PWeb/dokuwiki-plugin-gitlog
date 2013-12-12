<?php

require_once('config.inc.php');

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
	$c = GIT_EXEC.' --git-dir='.ROOT_DIR.$repo.' '.$command;
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
	return array_filter($input, 'remove_empty');
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
 * Main Function to get log and changed files
 * @param  string  $repo
 * @param  integer $limit
 * @param  boolean $bare
 * @return array
 */
function git_get_log($repo, $limit = 10, $bare=false)
{
	$format = array('%H', '%at', '%an', '%s');
	$params = implode(DELIMETER, $format);
	$data = run_git('log --pretty=format:"'.$params.'" -'.$limit, $repo, $bare);	
	$result = array();
	foreach($data as $line)
	{
		// explode
		$columns = explode(DELIMETER, $line);

		// run git show command
		$changedfiles = run_git('show --pretty="format:" --name-only '.$columns[0], $repo, $bare);

		$row = array(
			'commit' => $columns[0],
			'timestamp' => $columns[1],
			'author' => $columns[2],
			'message' => $columns[3],
			'changedfiles' => cleanup_git_show($changedfiles),
		);

		$result[] = $row;
	}

	return $result;
}
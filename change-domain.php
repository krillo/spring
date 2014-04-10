<?php
/**
 * For changing the domain name of a Wordpress 2.x/3.x installation
 * - noah.williamsson@gmail.com
 *
 * This script will update the following tables:
 * - wp_posts (guid, postcontent)
 * - wp_comments (comment_author_url)
 * - wp_options (siteurl, homeurl, fileuploads_url)
 * - wp_blogs (domain)
 * - wp_site (domain)
 * - wp_sitemeta (meta_value)
 * - wp_usermeta (meta_value)
 *
 * Things to configure before running:
 * - MySQL database settings
 * - Table prefix pattern
 * - Strings to find and replace (your domain name)
 *
 * Note:
 * - Backup your database before running this script
 *
 * Things to do after running this script:
 * - Update DOMAIN_CURRENT_SITE in wp-config.php if running multi-site
 *
 */


/**
 * MySQL settings
 */
$host = 'localhost';
$user = 'reptilo';
$pass = 'reptilo';
$dbname = 'sydmarincool';
$dbenc = 'utf8';

/**
 * Pattern used to find relevant WP tables
 */
$tablePrefixPattern = 'wp_%';

/**
 * String to find and string to replace with
 */
$findString = 'sydmarin.dev';
$replaceString = 'sydmarincool.dev';


/**
 * Recursively patch variable (used for serialized arrays and objects)
 */
function patch_r(&$var, $findString, $replaceString) {
	if(is_string($var))
		$var = str_replace($findString, $replaceString, $var);
	else if(is_array($var))
		foreach($var as $key => &$value)
			$var[$key] = patch_r($value, $findString, $replaceString);
	else if(is_object($var) || gettype($var) === 'object')
		foreach($var as $key => &$value)
			$var->$key = patch_r($value, $findString, $replaceString);
	return $var;
}


/**
 * Establish MySQL connect
 */
$m = new mysqli($host, $user, $pass, $dbname);
if(mysqli_connect_errno())
	die("Failed to connect to MySQL: ". mysqli_connect_error() ."\n");

$m->set_charset($dbenc);


/**
 * List tables
 */
$tables = array();
$q = 'SHOW TABLES LIKE "'. $tablePrefixPattern .'"';
$r = $m->query($q) or die($m->error . "\nSQL: $q\n");
while($row = $r->fetch_array())
	$tables[] = $row[0];

foreach($tables as $table) {

	/**
	 * Patch author URL in wp_comments (mainly for the demo comment)
	 */
	if(preg_match('@_comments$@', $table)) {

		$q = 'SELECT comment_ID, comment_author_url FROM '. $table;
		$r = $m->query($q) or die($m->error . "\nSQL: $q\n");

		echo "Considering $r->num_rows rows in table $table\n";
		while($row = $r->fetch_object()) {
			$url = str_replace($findString, $replaceString, $row->comment_author_url);

			$q = 'UPDATE '. $table .' SET comment_author_url="'. $m->escape_string($url) .'" WHERE comment_ID='. $row->comment_ID;
			$m->query($q) or die($m->error . "\nSQL: $q\n");
		}

		$r->close();
	}
	
	/**
	 * Patch wp_posts (guid and content)
	 */
	else if(preg_match('@_posts$@', $table)) {

		$q = 'SELECT ID, guid, post_content FROM '. $table;
		$r = $m->query($q) or die($m->error . "\nSQL: $q\n");

		echo "Considering $r->num_rows rows in table $table\n";
		while($row = $r->fetch_object()) {
			$guid = str_replace($findString, $replaceString, $row->guid);
			$content = str_replace($findString, $replaceString, $row->post_content);

			$q = 'UPDATE '. $table .' SET guid="'. $m->escape_string($guid) .'", post_content="'. $m->escape_string($content) .'" WHERE ID='. $row->ID;
			$m->query($q) or die($m->error . "\nSQL: $q\n");
		}

		$r->close();
	}

	/**
	 * Patch wp_options (fileupload_url, homeurl, siteurl, and serialized options)
	 */
	else if(preg_match('@_options$@', $table)) {

		$q = 'SELECT option_name, option_value FROM '. $table;
		$r = $m->query($q) or die($m->error . "\nSQL: $q\n");

		echo "Considering $r->num_rows rows in table $table\n";
		while($row = $r->fetch_object()) {
			if(!preg_match('@'. $findString .'@', $row->option_value))
				continue;

			echo "- Patching option $row->option_name\n";

			/**
			 * Attempt to unserialize option value.
			 * If it fails it's most likely a regular string
			 */
			$value = @unserialize($row->option_value);
			if($value !== FALSE)
				$value = serialize(patch_r($value, $findString, $replaceString));
			else
				$value = str_replace($findString, $replaceString, $row->option_value);

			$q = 'UPDATE '. $table .' SET option_value="'. $m->escape_string($value) .'" WHERE option_name="'. $m->escape_string($row->option_name) .'"';
			$m->query($q) or die($m->error . "\nSQL: $q\n");
		}

		$r->close();
	}

	/**
	 * Patch wp_blogs (domain) and wp_site (domain)
	 */
	else if(preg_match('@_(blogs|site)$@', $table, $matches)) {

		if($matches[1] === 'blogs')
			$idcolumn = 'blog_id';
		else
			$idcolumn = 'id';

		$q = 'SELECT '. $idcolumn .' AS id, domain FROM '. $table;
		$r = $m->query($q) or die($m->error . "\nSQL: $q\n");

		echo "Considering $r->num_rows rows in table $table\n";
		while($row = $r->fetch_object()) {
			if(!preg_match('@'. $findString .'@', $row->domain))
				continue;

			echo "- Patching ". $matches[1] ." domain $row->domain\n";

			$value = str_replace($findString, $replaceString, $row->domain);
			$q = 'UPDATE '. $table .' SET domain="'. $m->escape_string($value) .'" WHERE '. $idcolumn .'="'. $m->escape_string($row->id) .'"';
			$m->query($q) or die($m->error . "\nSQL: $q\n");
		}

		$r->close();
	}

	/**
	 * Patch wp_sitemeta (siteurl) and wp_usermeta (source_domain)
	 */
	else if(preg_match('@_(sitemeta|usermeta)$@', $table, $matches)) {

		if($matches[1] === 'sitemeta')
			$idcolumn = 'meta_id';
		else
			$idcolumn = 'umeta_id';

		$q = 'SELECT '. $idcolumn .' AS id, meta_key, meta_value FROM '. $table;
		$r = $m->query($q) or die($m->error . "\nSQL: $q\n");

		echo "Considering $r->num_rows rows in table $table\n";
		while($row = $r->fetch_object()) {
			if(!preg_match('@'. $findString .'@', $row->meta_value))
				continue;

			echo "- Patching ". $matches[1] ." key $row->meta_key\n";

			/**
			 * Attempt to unserialize option value.
			 * If it fails it's most likely a regular string
			 */
			$value = @unserialize($row->option_value);
			if($value !== FALSE)
				$value = serialize(patch_r($value, $findString, $replaceString));
			else
				$value = str_replace($findString, $replaceString, $row->option_value);

			$q = 'UPDATE '. $table .' SET meta_value="'. $m->escape_string($value) .'" WHERE '. $idcolumn .'="'. $m->escape_string($row->id) .'"';
			$m->query($q) or die($m->error . "\nSQL: $q\n");
		}

		$r->close();
	}

}

echo "Done\n";
echo "- Remember to update DOMAIN_CURRENT_SITE in wp-config if running multi-site\n";
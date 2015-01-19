<?php

/**
 * Extends another template, read more about template inheritance at {@link http://wiki.dwoo.org/index.php/TemplateInheritance}
 * <pre>
 *  * file : the template to extend
 * </pre>
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 *
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Jordi Boggiano
 * @license    http://dwoo.org/LICENSE   Modified BSD License
 * @link       http://dwoo.org/
 * @version    1.1.0
 * @date       2009-07-18
 * @package    Dwoo
 */
class Dwoo_Plugin_extends extends Dwoo_Plugin implements Dwoo_ICompilable
{
	protected static $childSource;
	protected static $regex;
	protected static $l;
	protected static $r;
	protected static $lastReplacement;

	public static function compile(Dwoo_Compiler $compiler, $file)
	{
		list($l, $r) = $compiler->getDelimiters();
		self::$l = preg_quote($l,'/');
		self::$r = preg_quote($r,'/');
		self::$regex = '/
			'.self::$l.'block\s(["\']?)(.+?)\1'.self::$r.'(?:\r?\n?)
			((?:
				(?R)
				|
				[^'.self::$l.']*
				(?:
					(?! '.self::$l.'\/?block\b )
					'.self::$l.'
					[^'.self::$l.']*+
				)*
			)*)
			'.self::$l.'\/block'.self::$r.'
			/six';

		if ($compiler->getLooseOpeningHandling()) {
			self::$l .= '\s*';
			self::$r = '\s*'.self::$r;
		}
		$inheritanceTree = array(array('source'=>$compiler->getTemplateSource()));
		$curPath = dirname($compiler->getDwoo()->getTemplate()->getResourceIdentifier()) . DIRECTORY_SEPARATOR;
		$curTpl = $compiler->getDwoo()->getTemplate();

		while (!empty($file)) {
			if ($file === '""' || $file === "''" || (substr($file, 0, 1) !== '"' && substr($file, 0, 1) !== '\'')) {
				throw new Dwoo_Compilation_Exception($compiler, 'Extends : The file name must be a non-empty string');
				return;
			}

			if (preg_match('#^["\']([a-z]{2,}):(.*?)["\']$#i', $file, $m)) {
				// resource:identifier given, extract them
				$resource = $m[1];
				$identifier = $m[2];
			} else {
				// get the current template's resource
				$resource = $curTpl->getResourceName();
				$identifier = substr($file, 1, -1);
			}

			try {
				$parent = $compiler->getDwoo()->templateFactory($resource, $identifier, null, null, null, $curTpl);
			} catch (Dwoo_Security_Exception $e) {
				throw new Dwoo_Compilation_Exception($compiler, 'Extends : Security restriction : '.$e->getMessage());
			} catch (Dwoo_Exception $e) {
				throw new Dwoo_Compilation_Exception($compiler, 'Extends : '.$e->getMessage());
			}

			if ($parent === null) {
				throw new Dwoo_Compilation_Exception($compiler, 'Extends : Resource "'.$resource.':'.$identifier.'" not found.');
			} elseif ($parent === false) {
				throw new Dwoo_Compilation_Exception($compiler, 'Extends : Resource "'.$resource.'" does not support extends.');
			}

			$curTpl = $parent;
			$newParent = array('source'=>$parent->getSource(), 'resource'=>$resource, 'identifier'=>$parent->getResourceIdentifier(), 'uid'=>$parent->getUid());
			if (array_search($newParent, $inheritanceTree, true) !== false) {
				throw new Dwoo_Compilation_Exception($compiler, 'Extends : Recursive template inheritance detected');
			}
			$inheritanceTree[] = $newParent;

			if (preg_match('/^'.self::$l.'extends(?:\(?\s*|\s+)(?:file=)?\s*((["\']).+?\2|\S+?)\s*\)?\s*?'.self::$r.'/i', $parent->getSource(), $match)) {
				$curPath = dirname($identifier) . DIRECTORY_SEPARATOR;
				if (isset($match[2]) && $match[2] == '"') {
					$file = '"'.str_replace('"', '\\"', substr($match[1], 1, -1)).'"';
				} elseif (isset($match[2]) && $match[2] == "'") {
					$file = '"'.substr($match[1], 1, -1).'"';
				} else {
					$file = '"'.$match[1].'"';
				}
			} else {
				$file = false;
			}
		}

		while (true) {
			$parent = array_pop($inheritanceTree);
			$child = end($inheritanceTree);
			self::$childSource = $child['source'];
			self::$lastReplacement = count($inheritanceTree) === 1;
			if (!isset($newSource)) {
				$newSource = $parent['source'];
			}
			$newSource = preg_replace_callback(self::$regex, array('Dwoo_Plugin_extends', 'replaceBlock'), $newSource);
			$newSource = $l.'do extendsCheck('.var_export($parent['resource'].':'.$parent['identifier'], true).')'.$r.$newSource;

			if (self::$lastReplacement) {
				break;
			}
		}
		$compiler->setTemplateSource($newSource);
		$compiler->recompile();
	}

	protected static function replaceBlock(array $matches)
	{
		$matches[3] = self::removeTrailingNewline($matches[3]);

		if (preg_match_all(self::$regex, self::$childSource, $override) && in_array($matches[2], $override[2])) {
			$key = array_search($matches[2], $override[2]);
			$override = self::removeTrailingNewline($override[3][$key]);

			$l = stripslashes(self::$l);
			$r = stripslashes(self::$r);

			if (self::$lastReplacement) {
				return preg_replace('/'.self::$l.'\$dwoo\.parent'.self::$r.'/is', $matches[3], $override);
			}
			return $l.'block '.$matches[1].$matches[2].$matches[1].$r.preg_replace('/'.self::$l.'\$dwoo\.parent'.self::$r.'/is', $matches[3], $override).$l.'/block'.$r;
		}

		if (preg_match(self::$regex, $matches[3])) {
			return preg_replace_callback(self::$regex, array('Dwoo_Plugin_extends', 'replaceBlock'), $matches[3] );
		}

		if (self::$lastReplacement) {
			return $matches[3];
		}

		return  $matches[0];
	}

	protected static function removeTrailingNewline($text)
	{
		return substr($text, -1) === "\n"
				? substr($text, -2, 1) === "\r"
					? substr($text, 0, -2)
					: substr($text, 0, -1)
				: $text;
	}
}
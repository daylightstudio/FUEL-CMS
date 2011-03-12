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
	protected static $l;
	protected static $lraw;
	protected static $r;
	protected static $rraw;
	protected static $lastReplacement;

	public static function compile(Dwoo_Compiler $compiler, $file)
	{
		list(self::$lraw, self::$rraw) = $compiler->getDelimiters();
		self::$l = preg_quote(self::$lraw,'/');
		self::$r = preg_quote(self::$rraw,'/');

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

			// TODO parse blocks tree for child source and new source
			// TODO replace blocks that are found in the child and in the parent recursively
			$firstL = preg_quote(self::$lraw[0], '/');
			$restL = preg_quote(substr(self::$lraw, 1), '/');
			$newSource = preg_replace_callback('/'.self::$l.'block (["\']?)(.+?)\1'.self::$r.'(?:\r?\n?)((?:[^'.$firstL.']*|'.$firstL.'(?!'.$restL.'\/block'.self::$r.'))*)'.self::$l.'\/block'.self::$r.'/is', array('Dwoo_Plugin_extends', 'replaceBlock'), $newSource);

			$newSource = self::$lraw.'do extendsCheck("'.$parent['resource'].':'.$parent['identifier'].'")'.self::$rraw.$newSource;

			if (self::$lastReplacement) {
				break;
			}
		}

		$compiler->setTemplateSource($newSource);
		$compiler->recompile();
	}

	protected static function replaceBlock(array $matches)
	{
		$matches[3] = self::cleanTrailingCRLF($matches[3]);
		$firstL = preg_quote(self::$lraw[0], '/');
		$restL = preg_quote(substr(self::$lraw, 1), '/');
		if (preg_match('/'.self::$l.'block (["\']?)'.preg_quote($matches[2],'/').'\1'.self::$r.'(?:\r?\n?)((?:[^'.$firstL.']*|'.$firstL.'(?!'.$restL.'\/block'.self::$r.'))*)'.self::$l.'\/block'.self::$r.'/is', self::$childSource, $override)) {
			$override[2] = self::cleanTrailingCRLF($override[2]);
			if (self::$lastReplacement) {
				return preg_replace('/'.self::$l.'\$dwoo\.parent'.self::$r.'/is', $matches[3], $override[2]);
			} else {
				return self::$lraw.'block '.$matches[1].$matches[2].$matches[1].self::$rraw.preg_replace('/'.self::$l.'\$dwoo\.parent'.self::$r.'/is', $matches[3], $override[2]).self::$lraw.'/block'.self::$rraw;
			}
		} else {
			if (self::$lastReplacement) {
				return $matches[3];
			} else {
				return $matches[0];
			}
		}
	}

	protected static function cleanTrailingCRLF($input)
	{
		if (substr($input, -1) === "\n") {
			if (substr($input, -2, 1) === "\r") {
				return substr($input, 0, -2);
			}
			return substr($input, 0, -1);
		}
		return $input;
	}
}

<?php

/**
 * Validates 'rel' attribute on <a> and <area> elements, as defined by the
 * HTML5 spec and the MicroFormats link type extensions tables.
 */
class HTMLPurifier_AttrDef_HTML5_ARel extends HTMLPurifier_AttrDef
{
    /**
     * Lookup table for valid values
     * @var array
     */
    protected static $values = array(
        // https://html.spec.whatwg.org/multipage/links.html#linkTypes
        'alternate' => true,
        'author' => true,
        'bookmark' => true,
        'external' => true,
        'help' => true,
        'license' => true,
        'next' => true,
        'nofollow' => true,
        'noopener' => true,
        'noreferrer' => true,
        'opener' => true,
        'prev' => true,
        'search' => true,
        'sidebar' => true,
        'tag' => true,
        // http://microformats.org/wiki/existing-rel-values#HTML5_link_type_extensions
        'acquaintance' => true,
        'amphtml' => true,
        'appendix' => true,
        'archived' => true,
        'attachment' => true,
        'canonical' => true,
        'category' => true,
        'chapter' => true,
        'child' => true,
        'co-resident' => true,
        'co-worker' => true,
        'code-license' => true,
        'code-repository' => true,
        'colleague' => true,
        'contact' => true,
        'content-license' => true,
        'content-repository' => true,
        'contents' => true,
        'copyright' => true,
        'crush' => true,
        'date' => true,
        'disclosure' => true,
        'discussion' => true,
        'enclosure' => true,
        'entry-content' => true,
        'first' => true,
        'friend' => true,
        'glossary' => true,
        'home' => true,
        'http://docs.oasis-open.org/ns/cmis/link/200908/acl' => true,
        'hub' => true,
        'in-reply-to' => true,
        'index' => true,
        'issues' => true,
        'jslicense' => true,
        'last' => true,
        'kin' => true,
        'lightbox' => true,
        'lightvideo' => true,
        'me' => true,
        'met' => true,
        'muse' => true,
        'neighbor' => true,
        'parent' => true,
        'prerender' => true,
        'previous' => true,
        'profile' => true,
        'publisher' => true,
        'radioepg' => true,
        'rendition' => true,
        'reply-to' => true,
        'root' => true,
        'section' => true,
        'sibling' => true,
        'spouse' => true,
        'start' => true,
        'subsection' => true,
        'sweetheart' => true,
        'syndication' => true,
        'toc' => true,
        'transformation' => true,
        'webmention' => true,
        'widget' => true,
    );

    /**
     * Return lookup table for valid 'rel' values
     *
     * @return array
     * @codeCoverageIgnore
     */
    public static function values()
    {
        return self::$values;
    }

    /**
     * @var array
     */
    protected $allowed;

    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        if ($this->allowed === null) {
            $allowedRel = (array) $config->get('Attr.AllowedRel');
            if (empty($allowedRel)) {
                $allowed = array();
            } else {
                $allowed = array_intersect_key($allowedRel, self::$values);
            }
            $this->allowed = $allowed;
        }

        $string = $this->parseCDATA($string);
        $parts = explode(' ', $string);

        $result = array();
        foreach ($parts as $part) {
            $part = strtolower(trim($part));
            if (!isset($this->allowed[$part])) {
                continue;
            }
            $result[$part] = true;
        }

        if (empty($result)) {
            return false;
        }

        return implode(' ', array_keys($result));
    }
}

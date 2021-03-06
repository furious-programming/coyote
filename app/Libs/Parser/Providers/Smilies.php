<?php

namespace Coyote\Parser\Providers;

/**
 * Class Smilies
 * @package Coyote\Parser\Providers
 */
class Smilies implements ProviderInterface
{
    use Hash;

    private $smilies = [
        ':)'            => 'smile.gif'
    ];

    /**
     * @param string $text
     * @return string
     */
    public function parse($text)
    {
        $text = $this->hashBlock($text, ['code', 'a']);
        $text = $this->hashInline($text, 'img');

        $patterns = $replacements = [];

        while (list($var, $value) = each($this->smilies)) {
            $patterns[] = '#(?<=^|[\n ]|\.)' . preg_quote($var, '#') . '#';
            $replacements[] = '<img alt="' . $var . '" title="' . $var . '" src="' . asset('img/smilies/' . $value) . '" />';
        }
        reset($this->smilies);

        $text = substr(preg_replace($patterns, $replacements, ' ' . $text . ' '), 1, -1);
        $text = $this->unhash($text);

        return $text;
    }
}

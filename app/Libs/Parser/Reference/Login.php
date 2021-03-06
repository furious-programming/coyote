<?php

namespace Coyote\Parser\Reference;

class Login
{
    /**
     * @param $html
     * @return array
     */
    public function grab($html)
    {
        $dom = new \DOMDocument;
        $dom->loadHTML($html);

        $links = $dom->getElementsByTagName('a');
        $usersId = [];

        foreach ($links as $link) {
            if ('@' === $link->nodeValue[0]
                && preg_match('~.*/Profile/([0-9]+)~', $link->getAttribute('href'), $match)) {
                $usersId[] = $match[1];
            }
        }

        return array_unique($usersId);
    }
}

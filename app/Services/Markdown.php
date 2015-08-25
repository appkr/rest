<?php

namespace app\Services;

use ParsedownExtra;

/**
 * Class Markdown
 *
 * @package Appkr\Services\Compilers
 */
class Markdown extends ParsedownExtra {

    // Route name to generate in replace for @username
    const ROUTE_USER = 'users.show-by-username';

    // Pattern to search for DebugMessages mention
    const PATTERN_USER = '/(?:^|[^\w+])@(?P<username>[\pL\pN]+)/';

    // Pattern to search for email
    const PATTERN_EMAIL = '/(?P<email>[[:alnum:]_.+-]+@[[:alnum:]-]+\.[[:alnum:]-.]+)/';

    /**
     * Add issue list parsing functionality
     *
     * @param $text
     * @return mixed|string
     */
    function text($text) {
        if (preg_match(self::PATTERN_USER, $text, $matches) > 0) {
            $text = preg_replace_callback(self::PATTERN_USER, function ($matches) {
                return link_to_route(self::ROUTE_USER, $matches[0], [$matches['username']]);
            }, $text);
        }

        if (preg_match(self::PATTERN_EMAIL, $text, $matches) > 0) {
            $text = preg_replace_callback(self::PATTERN_EMAIL, function ($matches) {
                return link_to("mailto:{$matches['email']}", $matches['email']);
            }, $text);
        }

        return parent::text($text);
    }

}
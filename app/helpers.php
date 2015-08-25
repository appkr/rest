<?php

if (! function_exists('has_error')) {
    /**
     * Build has-error class
     *
     * @param $field
     * @param $errors
     * @return string
     */
    function has_error($field, $errors) {
        return isset($errors) && $errors->has($field) ? 'has-error' : '';
    }
}

if (! function_exists('first_error')) {
    /**
     * Build form error message
     *
     * @param $field
     * @param $errors
     * @return null
     */
    function first_error($field, $errors) {
        return isset($errors)
            ? $errors->first($field, '<span class="form-error">:message</span>')
            : null;
    }
}

if (! function_exists('jwt_factory')) {
    /**
     * Get a \Tymon\JWTAuth\PayloadFactory instance
     *
     * @return \Tymon\JWTAuth\PayloadFactory
     */
    function jwt_fractory() {
        return app(\Tymon\JWTAuth\PayloadFactory::class);
    }
}

if (! function_exists('jwt_auth')) {
    /**
     * Get a JWTAuth instance
     *
     * @return \Tymon\JWTAuth\JWTAuth
     */
    function jwt_auth() {
        return app(\Tymon\JWTAuth\JWTAuth::class);
    }
}

if (! function_exists('markdown')) {
    /**
     * Convert some text to Markdown...
     *
     * @param $text
     *
     * @return string
     */
    function markdown($text)
    {
        return app()->text($text);
    }
}

if (! function_exists('is_api_request')) {
    /**
     * Determine if the current request is from an api client
     *
     * @return mixed
     */
    function is_api_request()
    {
        return Request::is(config('fractal.pattern'));
    }
}

if (! function_exists('save_image')) {
    /**
     * Resize and save image file
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param                                                     $path
     * @param string                                              $prefix
     * @param int                                                 $width
     *
     * @return array|bool
     * @throws Exception
     */
    function save_image(Symfony\Component\HttpFoundation\File\UploadedFile $file, $path, $prefix = 'img', $width = 720)
    {
        if (! class_exists('Image')) {
            // We need intervention/image package
            throw new Exception('Intervention Image class required!');
        }

        $fileName = sprintf("%s_%d_%s", $prefix, time(), $file->getClientOriginalName());
        $savePath = "{$path}/{$fileName}";

        if (File::exists($savePath)) {
            throw new Exception('Image with a same name already exists!');
        }

        $image = Image::make($file->getRealPath());

        if ($image->width() > $width) {
            $image->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        if ($image->save($savePath)) {
            return [
                'filename' => $fileName,
                'size'     => $file->getSize()
            ];
        }

        return false;
    }
}

if (! function_exists('save_file')) {
    /**
     * Save given file
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param                                                     $path
     *
     * @return array
     * @throws Exception
     */
    function save_file(Symfony\Component\HttpFoundation\File\UploadedFile $file, $path)
    {
        $fileName = sprintf("%d_%s", time(), str_replace(' ', '_', $file->getClientOriginalName()));

        if (File::exists("{$path}/{$fileName}")) {
            throw new Exception('File already exists!');
        }

        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 755, true);
        }

        if (! $file->move($path, $fileName)) {
            throw new Exception('Failed to save!');
        }

        return [
            'filename' => $fileName,
            'size'     => $file->getSize()
        ];
    }
}

if (! function_exists('icon')) {
    /**
     * Generate Font Awesome icon
     *
     * @param string $class Font Awesome class name
     * @param string $additionalClass
     *
     * @return string
     * @throws Exception
     */
    function icon($class, $additionalClass = null)
    {
        if (! File::exists(config_path('icons.php'))) {
            throw new Exception('config/icons.php required!');
        }

        $faClass = config("icons.{$class}");

        return "<i class=\"{$faClass} {$additionalClass}\"></i>";
    }
}

if (! function_exists('is_active_sort')) {
    /**
     * Determine if the given $column is active
     *
     * @param $column
     *
     * @return bool
     */
    function is_active_sort($column)
    {
        return Input::get('order') == $column
            ? true
            : false;
    }
}

if (! function_exists('get_active_sort_icon')) {
    /**
     * Calculate a html tag for sort icon
     *
     * @param $column
     *
     * @return null|string
     */
    function get_active_sort_icon($column)
    {
        if (! Input::get('order') && (in_array($column, ['created_at']))) {
            return icon('order_desc');
        }

        $icon = (Input::get('direction') == 'asc')
            ? icon('order_asc')
            : icon('order_desc');

        return is_active_sort($column)
            ? $icon
            : null;
    }
}

if (! function_exists('get_sort_link')) {
    /**
     * Calculate an anchor tag for toggling a sort link
     *
     * @param       $column
     * @param       $text
     * @param array $append query $params strings to append
     *
     * @return mixed
     */
    function get_sort_link($column, $text, $append = [])
    {
        $direction = (Input::get('direction') == 'asc') ? 'desc' : 'asc';

        $queryString = http_build_query(array_merge(
            Input::except(['page', 'order', 'direction']),
            ['order' => $column, 'direction' => $direction],
            $append
        ));

        $url  = sprintf("%s?%s", Request::url(), $queryString);
        $text = sprintf("%s %s", get_active_sort_icon($column), $text);

        $markup = link_to($url, $text);
        // Native link_to() function escapes html special chars
        // So, we need to replace them. (e.g. <i class="fa fa-icon"></i>)

        return str_replace(['&lt;', '&gt;', '&quot;'], ['<', '>', '"'], $markup);
    }
}

if (! function_exists('get_gravatar_url')) {
    /**
     * Get gravatar image url
     *
     * @param  string  $email
     * @param  integer $size
     *
     * @return string
     */
    function get_gravatar_url($email, $size = 72)
    {
        return sprintf("//www.gravatar.com/avatar/%s?s=%d", md5($email), $size);
    }
}

if (! function_exists('get_profile_url')) {
    /**
     * Get gravatar profile page url
     *
     * @param  string $email
     *
     * @return string
     */
    function get_profile_url($email)
    {
        return sprintf("//www.gravatar.com/%s", md5($email));
    }
}

if (! function_exists('is_mobile_browser')) {
    /**
     * Determine if the client browser is mobile
     *
     * @return bool
     */
    function is_mobile_browser()
    {
        $ua = strtolower(Request::server('HTTP_USER_AGENT'));

        return ($ua)
        && (str_contains($ua, 'android') || str_contains($ua, 'iphone'))
            ? true
            : false;
    }
}

if (! function_exists('get_file_size')) {
    /**
     * Calculate human readable file size string
     *
     * @param int $filesize
     *
     * @return string
     */
    function get_file_size($filesize)
    {
        if (! is_numeric($filesize)) {
            return 'NaN';
        }

        $decr   = 1024;
        $step   = 0;
        $suffix = ['b', 'KB', 'MB', 'GB'];

        while (($filesize / $decr) > 0.9) {
            $filesize = $filesize / $decr;
            $step++;
        }

        return round($filesize, 2) . $suffix[$step];
    }
}

if (! function_exists('get_client_language')) {
    /**
     * Get the client's language preference
     * by parsing Accept-Language HTTP header
     *
     * @return array
     */
    function get_client_language()
    {
        $pattern = '/^(?P<primarytag>[a-zA-Z]{2,8})'
            . '(?:-(?P<subtag>[a-zA-Z]{2,8}))?(?:(?:;q=)'
            . '(?P<quantifier>\d\.\d))?$/';

        $languages  = [];
        $preference = Request::server('HTTP_ACCEPT_LANGUAGE');

        foreach (explode(',', $preference) as $language) {
            $splits      = [];
            $languages[] = preg_match($pattern, $language, $splits) ? $splits : null;
        }

        return $languages;
    }
}

if (! function_exists('get_star_rating')) {
    /**
     * Calculate star rating
     *
     * @param int    $score
     * @param string $on
     * @param string $off
     *
     * @return string
     */
    function get_star_rating($score, $on = '★', $off = '☆')
    {
        $score = round($score);

        return str_repeat($on, $score) . str_repeat($off, 5 - $score);
    }
}

if (! function_exists('custom_path')) {
    /**
     * @param string $path
     *
     * @return string
     */
    function custom_path($path = '')
    {
        return base_path()
        . '/custom'
        . ($path ? "/{$path}" : $path);
    }
}

if (! function_exists('is_current_route')) {
    /**
     * Determine if the given routeName is currentRouteName
     *
     * @param string $routeName
     *
     * @return bool
     */
    function is_current_route($routeName)
    {
        return Route::currentRouteName() == $routeName;
    }
}

if (! function_exists('get_path_from_route')) {
    /**
     * Calculate the path from a named route
     *
     * @param $routeName
     *
     * @return string
     */
    function get_path_from_route($routeName)
    {
        $path = parse_url(route($routeName), PHP_URL_PATH);

        return trim($path, '/');
    }
}

if (! function_exists('is_redundant_request')) {
    /**
     * Determine if the current request is redundant request
     * It was observed that there would be a dummy request in chrome
     * especially when the client download a file
     *
     * @return bool
     */
    function is_redundant_request()
    {
        $duplicate = App\Logs::where('module', Route::currentRouteName())
            ->where('ip', Request::server('REMOTE_ADDR'))
            ->where('created_at', '>', Carbon\Carbon::now()->subSeconds(10))
            ->take(5)->lists('id');

        return $duplicate ? true : false;
    }
}

<?php

namespace HexideDigital\HexideAdmin\Traits\Models;

use Illuminate\Container\Container;
use Illuminate\Support\Str;

trait Includeble
{
    protected $requestedIncludes=[];

    protected function hasInclude($include): bool
    {

        $request = Container::getInstance()->make('request');
        if ($request->query->has('include')) {
            $this->parseIncludes(str_replace(' ','', Str::lower($request->input('include'))));
        }
        return in_array($include, $this->requestedIncludes);
    }

    /**
     * Parse Include String.
     *
     * @param array|string $includes Array or csv string of resources to include
     *
     * @return $this
     */
    protected function parseIncludes($includes)
    {
        // Wipe these before we go again
        $this->requestedIncludes = [];

        if (is_string($includes)) {
            $includes = explode(',', $includes);
        }

        if (!is_array($includes)) {
            throw new \InvalidArgumentException(
                    'The parseIncludes() method expects a string or an array. ' . gettype($includes) . ' given'
            );
        }

        $this->requestedIncludes = $includes;

        return $this;
    }
}

<?php

namespace RightCapital\FlySystem\Support;

class ContentListingFormatter
{
    private $directory;
    private $recursive;

    public function __construct($directory, $recursive)
    {
        $this->directory = $directory;
        $this->recursive = $recursive;
    }

    public function formatListing(array $listing)
    {
        $listing = array_values(array_map([$this, 'addPathInfo'], array_filter($listing, 'isEntryOutOfScope')));

        return $this->sortListing($listing);
    }

    private function sortListing(array $listing)
    {
        usort($listing, function ($a, $b) {
            return strcasecmp($a['path'], $b['path']);
        });

        return $listing;
    }
}
<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class SlugGenerator
{
    public function __construct(
        private SluggerInterface $slugger
    ) {
    }

    /**
     * Generate a unique slug from a title
     *
     * @param string $title The title to slugify
     * @param int|null $uniqueId Optional unique ID to append for uniqueness
     * @return string The generated slug
     */
    public function generateFromTitle(string $title, ?int $uniqueId = null): string
    {
        $slug = $this->slugger->slug($title)->lower()->toString();

        if ($uniqueId !== null) {
            $slug .= '-' . $uniqueId;
        }

        return $slug;
    }
}

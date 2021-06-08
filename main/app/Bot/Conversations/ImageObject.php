<?php

namespace App\Bot\Conversations;

/**
 * Class ImageObject.
 */
class ImageObject
{
    private string $imageUrl;

    public function __construct(string $imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return string
     */
    public function getRealPath(): string
    {
        return $this->imageUrl;
    }

    /**
     * @return string|string[]
     */
    public function getClientOriginalExtension()
    {
        return pathinfo($this->imageUrl, PATHINFO_EXTENSION);
    }
}

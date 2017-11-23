<?php

namespace Caffeinated\Modules\Repositories;

class LocalRepository extends Repository
{

    /**
     * Returns the entire module repository content as a collection.
     *
     * @return Collection
     */
    public function load()
    {
        return $this->getCache();
    }

    /**
     * Saves the content to the repository.
     *
     * @param $content
     * @return int|bool a non-zero or true value on success, or false on failure.
     */
    public function save($content)
    {
        $cachePath = $this->getCachePath();
        return $this->files->put($cachePath, $content);
    }

    /**
     * Get the contents of the cache file.
     *
     * @return Collection
     */
    private function getCache()
    {
        $cachePath = $this->getCachePath();

        if (!$this->files->exists($cachePath)) {
            $this->createCache();

            $this->optimize();
        }

        return collect(json_decode($this->files->get($cachePath), true));
    }

    /**
     * Create an empty instance of the cache file.
     *
     * @return Collection
     */
    private function createCache()
    {
        $cachePath = $this->getCachePath();
        $content = json_encode([], JSON_PRETTY_PRINT);

        $this->files->put($cachePath, $content);

        return collect(json_decode($content, true));
    }

    /**
     * Get the path to the cache file.
     *
     * @return string
     */
    private function getCachePath()
    {
        return storage_path('app/modules.json');
    }
}

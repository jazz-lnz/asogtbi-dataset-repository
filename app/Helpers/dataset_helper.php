<?php

if (! function_exists('dataset_cover_url')) {
    /**
     * @param array<string, mixed> $dataset
     */
    function dataset_cover_url(array $dataset): string
    {
        if (! empty($dataset['cover_image']) && ! empty($dataset['id'])) {
            return site_url('datasets/' . (int) $dataset['id'] . '/cover');
        }

        return base_url('assets/img/placeholders/dataset-placeholder-img.png');
    }
}

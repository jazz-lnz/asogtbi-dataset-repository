<?php

if (! function_exists('metadata_similarity_score')) {
    function metadata_similarity_score(array $current, array $candidate): int
    {
        $score = 0;

        if (($current['category'] ?? null) && strcasecmp((string) $current['category'], (string) ($candidate['category'] ?? '')) === 0) {
            $score += 30;
        }

        if (($current['data_type'] ?? null) && strcasecmp((string) $current['data_type'], (string) ($candidate['data_type'] ?? '')) === 0) {
            $score += 15;
        }

        if (($current['file_format'] ?? null) && strcasecmp((string) $current['file_format'], (string) ($candidate['file_format'] ?? '')) === 0) {
            $score += 10;
        }

        $currentTags = array_filter(array_map('trim', explode(',', strtolower($current['tags'] ?? ''))));
        $candidateTags = array_filter(array_map('trim', explode(',', strtolower($candidate['tags'] ?? ''))));
        $allTags = array_unique(array_merge($currentTags, $candidateTags));
        if ($allTags !== []) {
            $score += (int) round((count(array_intersect($currentTags, $candidateTags)) / count($allTags)) * 30);
        }

        $currentWords = array_unique(str_word_count(strtolower((string) ($current['description'] ?? '')), 1));
        $candidateWords = array_unique(str_word_count(strtolower((string) ($candidate['description'] ?? '')), 1));
        $allWords = array_unique(array_merge($currentWords, $candidateWords));
        if ($allWords !== []) {
            $score += (int) round((count(array_intersect($currentWords, $candidateWords)) / count($allWords)) * 15);
        }

        return $score;
    }
}

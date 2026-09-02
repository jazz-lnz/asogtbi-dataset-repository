<?php

if (! function_exists('recommendation_tokens')) {
    /**
     * @return string[]
     */
    function recommendation_tokens(string $value): array
    {
        $stopWords = array_fill_keys([
            'a', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'data', 'dataset',
            'datasets', 'for', 'from', 'in', 'into', 'is', 'of', 'on', 'or',
            'record', 'records', 'research', 'sample', 'the', 'to', 'with',
            'analysis', 'metric', 'metrics', 'dummy', 'import',
        ], true);

        $parts = preg_split('/[^a-z0-9]+/', strtolower($value), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $tokens = [];
        $aliases = [
            'twitter' => ['social', 'media'],
            'instagram' => ['social', 'media'],
            'facebook' => ['social', 'media'],
            'linkedin' => ['social', 'media'],
            'youtube' => ['social', 'media'],
            'snapchat' => ['social', 'media'],
            'tiktok' => ['social', 'media'],
            'pinterest' => ['social', 'media'],
            'reddit' => ['social', 'media'],
            'crop' => ['agriculture', 'farm'],
            'agricultural' => ['agriculture', 'farm'],
            'agriculture' => ['farm'],
            'farm' => ['agriculture'],
        ];

        foreach ($parts as $token) {
            if (isset($stopWords[$token]) || ctype_digit($token) || strlen($token) < 2) {
                continue;
            }

            if (strlen($token) > 4 && str_ends_with($token, 'ies')) {
                $token = substr($token, 0, -3) . 'y';
            } elseif (strlen($token) > 4 && str_ends_with($token, 's') && ! str_ends_with($token, 'ss')) {
                $token = substr($token, 0, -1);
            }

            $tokens[$token] = true;
            foreach ($aliases[$token] ?? [] as $alias) {
                $tokens[$alias] = true;
            }
        }

        return array_keys($tokens);
    }
}

if (! function_exists('recommendation_jaccard')) {
    /**
     * @param string[] $left
     * @param string[] $right
     */
    function recommendation_jaccard(array $left, array $right): float
    {
        $union = array_unique(array_merge($left, $right));
        if ($union === []) {
            return 0.0;
        }

        return count(array_intersect($left, $right)) / count($union);
    }
}

if (! function_exists('metadata_similarity_score')) {
    function metadata_similarity_score(array $current, array $candidate): int
    {
        $score = 0;

        $score += (int) round(recommendation_jaccard(
            recommendation_tokens((string) ($current['title'] ?? '')),
            recommendation_tokens((string) ($candidate['title'] ?? ''))
        ) * 35);
        $score += (int) round(recommendation_jaccard(
            recommendation_tokens((string) ($current['tags'] ?? '')),
            recommendation_tokens((string) ($candidate['tags'] ?? ''))
        ) * 30);
        $score += (int) round(recommendation_jaccard(
            recommendation_tokens((string) ($current['description'] ?? '')),
            recommendation_tokens((string) ($candidate['description'] ?? ''))
        ) * 15);

        $category = trim(strtolower((string) ($current['category'] ?? '')));
        $candidateCategory = trim(strtolower((string) ($candidate['category'] ?? '')));
        if ($category !== ''
            && $category === $candidateCategory
            && ! in_array($category, ['dummy data', 'general', 'uncategorized'], true)) {
            $score += 10;
        }

        if (($current['data_type'] ?? null)
            && strcasecmp((string) $current['data_type'], (string) ($candidate['data_type'] ?? '')) === 0) {
            $score += 5;
        }

        if (($current['file_format'] ?? null)
            && strcasecmp((string) $current['file_format'], (string) ($candidate['file_format'] ?? '')) === 0) {
            $score += 5;
        }

        return min(100, $score);
    }
}

if (! function_exists('search_relevance_score')) {
    function search_relevance_score(string $search, array $dataset): int
    {
        $queryTokens = recommendation_tokens($search);
        if ($queryTokens === []) {
            return 0;
        }

        $queryCount = count($queryTokens);
        $weightedFields = [
            'title' => 35,
            'tags' => 30,
            'category' => 15,
            'description' => 15,
            'data_type' => 2,
            'content_formats' => 2,
            'file_format' => 1,
        ];

        $score = 0.0;
        foreach ($weightedFields as $field => $weight) {
            $fieldTokens = recommendation_tokens((string) ($dataset[$field] ?? ''));
            $matched = count(array_intersect($queryTokens, $fieldTokens));
            $score += ($matched / $queryCount) * $weight;
        }

        return min(100, (int) round($score));
    }
}

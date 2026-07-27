<?php

if (! function_exists('dataset_citation')) {
    function dataset_citation(array $dataset): string
    {
        $title = $dataset['title'] ?? 'Untitled Dataset';
        $author = trim((string) ($dataset['author'] ?? $dataset['contributor'] ?? ''));
        $year = $dataset['year'] ?? date('Y');
        $publisher = $dataset['publisher'] ?? 'ASOG TBI Dataset Repository';

        if ($author === '') {
            return "{$title}. ({$year}). {$publisher}.";
        }

        return "{$author}. ({$year}). {$title}. {$publisher}.";
    }
}

if (! function_exists('dataset_apa_citation')) {
    function dataset_apa_citation(array $dataset): string
    {
        $title = $dataset['title'] ?? 'Untitled Dataset';
        $author = trim((string) ($dataset['author'] ?? $dataset['contributor'] ?? ''));
        $year = $dataset['year'] ?? date('Y');
        $publisher = $dataset['publisher'] ?? 'ASOG TBI Dataset Repository';
        $doi = trim((string) ($dataset['doi'] ?? ''));
        $url = trim((string) ($dataset['url'] ?? ''));

        if ($author === '') {
            $author = 'ASOG TBI';
        }

        $citation = "{$author}. ({$year}). {$title} [Data set]. {$publisher}.";

        if ($doi !== '') {
            $citation .= " https://doi.org/{$doi}";
        } elseif ($url !== '') {
            $citation .= " {$url}";
        }

        return $citation;
    }
}

if (! function_exists('dataset_mla_citation')) {
    function dataset_mla_citation(array $dataset): string
    {
        $title = $dataset['title'] ?? 'Untitled Dataset';
        $author = trim((string) ($dataset['author'] ?? $dataset['contributor'] ?? ''));
        $year = $dataset['year'] ?? date('Y');
        $publisher = $dataset['publisher'] ?? 'ASOG TBI Dataset Repository';
        $doi = trim((string) ($dataset['doi'] ?? ''));
        $url = trim((string) ($dataset['url'] ?? ''));

        if ($author === '') {
            $author = 'ASOG TBI';
        }

        $citation = "{$author}. \"{$title}.\" {$publisher}, {$year}.";

        if ($doi !== '') {
            $citation .= " https://doi.org/{$doi}.";
        } elseif ($url !== '') {
            $citation .= " {$url}.";
        }

        return $citation;
    }
}

if (! function_exists('dataset_acm_citation')) {
    function dataset_acm_citation(array $dataset): string
    {
        $title = $dataset['title'] ?? 'Untitled Dataset';
        $author = trim((string) ($dataset['author'] ?? $dataset['contributor'] ?? ''));
        $year = $dataset['year'] ?? date('Y');
        $publisher = $dataset['publisher'] ?? 'ASOG TBI Dataset Repository';
        $doi = trim((string) ($dataset['doi'] ?? ''));
        $url = trim((string) ($dataset['url'] ?? ''));

        if ($author === '') {
            $author = 'ASOG TBI';
        }

        $citation = "{$author}. {$year}. {$title}. {$publisher}.";

        if ($doi !== '') {
            $citation .= " https://doi.org/{$doi}";
        } elseif ($url !== '') {
            $citation .= " {$url}";
        }

        return $citation;
    }
}

if (! function_exists('dataset_bibtex')) {
    function dataset_bibtex(array $dataset): string
    {
        $key = preg_replace('/[^A-Za-z0-9]+/', '', $dataset['title'] ?? 'dataset');
        $year = $dataset['year'] ?? date('Y');
        $title = $dataset['title'] ?? 'Untitled Dataset';
        $author = trim((string) ($dataset['author'] ?? $dataset['contributor'] ?? ''));
        $publisher = $dataset['publisher'] ?? 'ASOG TBI Dataset Repository';
        $doi = trim((string) ($dataset['doi'] ?? ''));
        $url = trim((string) ($dataset['url'] ?? ''));
        $authorLine = $author !== '' ? "\n  author = {{$author}}," : '';
        $doiLine = $doi !== '' ? "\n  doi = {{$doi}}," : '';
        $urlLine = ($url !== '' && $doi === '') ? "\n  url = {{$url}}," : '';

        return "@misc{{$key}{$year},{$authorLine}\n  title = {{$title}},\n  year = {{$year}},{$doiLine}{$urlLine}\n  publisher = {{$publisher}}\n}";
    }
}

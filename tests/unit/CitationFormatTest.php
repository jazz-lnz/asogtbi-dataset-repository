<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class CitationFormatTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        helper(['citation']);
    }

    // ─── APA Citation Tests ──────────────────────────────────────────────

    public function testApaCitationWithAuthorAndYear(): void
    {
        $citation = dataset_apa_citation([
            'title' => 'Sample Dataset',
            'author' => 'John Smith',
            'year' => '2026',
        ]);

        $this->assertSame(
            'John Smith. (2026). Sample Dataset [Data set]. ASOG TBI Dataset Repository.',
            $citation
        );
    }

    public function testApaCitationWithoutAuthorUsesDefault(): void
    {
        $citation = dataset_apa_citation([
            'title' => 'Sample Dataset',
            'author' => '',
            'year' => '2026',
        ]);

        $this->assertSame(
            'ASOG TBI. (2026). Sample Dataset [Data set]. ASOG TBI Dataset Repository.',
            $citation
        );
    }

    public function testApaCitationWithDoi(): void
    {
        $citation = dataset_apa_citation([
            'title' => 'Research Data',
            'author' => 'Jane Doe',
            'year' => '2025',
            'doi' => '10.1234/example',
        ]);

        $this->assertSame(
            'Jane Doe. (2025). Research Data [Data set]. ASOG TBI Dataset Repository. https://doi.org/10.1234/example',
            $citation
        );
    }

    public function testApaCitationWithUrlButNoDoi(): void
    {
        $citation = dataset_apa_citation([
            'title' => 'Online Data',
            'author' => 'Bob Wilson',
            'year' => '2024',
            'url' => 'https://example.com/data',
        ]);

        $this->assertSame(
            'Bob Wilson. (2024). Online Data [Data set]. ASOG TBI Dataset Repository. https://example.com/data',
            $citation
        );
    }

    public function testApaCitationDoiTakesPrecedenceOverUrl(): void
    {
        $citation = dataset_apa_citation([
            'title' => 'Dataset with Both',
            'author' => 'Alice Brown',
            'year' => '2023',
            'doi' => '10.5678/test',
            'url' => 'https://example.com/fallback',
        ]);

        $this->assertStringContainsString('https://doi.org/10.5678/test', $citation);
        $this->assertStringNotContainsString('https://example.com/fallback', $citation);
    }

    public function testApaCitationDefaultPublisher(): void
    {
        $citation = dataset_apa_citation([
            'title' => 'Test Dataset',
            'author' => 'Test Author',
            'year' => '2026',
        ]);

        $this->assertStringContainsString('ASOG TBI Dataset Repository', $citation);
    }

    public function testApaCitationCustomPublisher(): void
    {
        $citation = dataset_apa_citation([
            'title' => 'Custom Dataset',
            'author' => 'Custom Author',
            'year' => '2026',
            'publisher' => 'Custom Publisher',
        ]);

        $this->assertStringContainsString('Custom Publisher', $citation);
    }

    // ─── MLA Citation Tests ──────────────────────────────────────────────

    public function testMlaCitationWithAuthorAndYear(): void
    {
        $citation = dataset_mla_citation([
            'title' => 'Sample Dataset',
            'author' => 'John Smith',
            'year' => '2026',
        ]);

        $this->assertSame(
            'John Smith. "Sample Dataset." ASOG TBI Dataset Repository, 2026.',
            $citation
        );
    }

    public function testMlaCitationWithoutAuthorUsesDefault(): void
    {
        $citation = dataset_mla_citation([
            'title' => 'Sample Dataset',
            'author' => '',
            'year' => '2026',
        ]);

        $this->assertSame(
            'ASOG TBI. "Sample Dataset." ASOG TBI Dataset Repository, 2026.',
            $citation
        );
    }

    public function testMlaCitationWithDoi(): void
    {
        $citation = dataset_mla_citation([
            'title' => 'Research Data',
            'author' => 'Jane Doe',
            'year' => '2025',
            'doi' => '10.1234/example',
        ]);

        $this->assertSame(
            'Jane Doe. "Research Data." ASOG TBI Dataset Repository, 2025. https://doi.org/10.1234/example.',
            $citation
        );
    }

    public function testMlaCitationWithUrlButNoDoi(): void
    {
        $citation = dataset_mla_citation([
            'title' => 'Online Data',
            'author' => 'Bob Wilson',
            'year' => '2024',
            'url' => 'https://example.com/data',
        ]);

        $this->assertSame(
            'Bob Wilson. "Online Data." ASOG TBI Dataset Repository, 2024. https://example.com/data.',
            $citation
        );
    }

    public function testMlaCitationDoiTakesPrecedenceOverUrl(): void
    {
        $citation = dataset_mla_citation([
            'title' => 'Dataset with Both',
            'author' => 'Alice Brown',
            'year' => '2023',
            'doi' => '10.5678/test',
            'url' => 'https://example.com/fallback',
        ]);

        $this->assertStringContainsString('https://doi.org/10.5678/test', $citation);
        $this->assertStringNotContainsString('https://example.com/fallback', $citation);
    }

    public function testMlaCitationUsesQuotesForTitle(): void
    {
        $citation = dataset_mla_citation([
            'title' => 'Quoted Title',
            'author' => 'Test Author',
            'year' => '2026',
        ]);

        $this->assertStringContainsString('"Quoted Title."', $citation);
    }

    // ─── ACM Citation Tests ──────────────────────────────────────────────

    public function testAcmCitationWithAuthorAndYear(): void
    {
        $citation = dataset_acm_citation([
            'title' => 'Sample Dataset',
            'author' => 'John Smith',
            'year' => '2026',
        ]);

        $this->assertSame(
            'John Smith. 2026. Sample Dataset. ASOG TBI Dataset Repository.',
            $citation
        );
    }

    public function testAcmCitationWithoutAuthorUsesDefault(): void
    {
        $citation = dataset_acm_citation([
            'title' => 'Sample Dataset',
            'author' => '',
            'year' => '2026',
        ]);

        $this->assertSame(
            'ASOG TBI. 2026. Sample Dataset. ASOG TBI Dataset Repository.',
            $citation
        );
    }

    public function testAcmCitationWithDoi(): void
    {
        $citation = dataset_acm_citation([
            'title' => 'Research Data',
            'author' => 'Jane Doe',
            'year' => '2025',
            'doi' => '10.1234/example',
        ]);

        $this->assertSame(
            'Jane Doe. 2025. Research Data. ASOG TBI Dataset Repository. https://doi.org/10.1234/example',
            $citation
        );
    }

    public function testAcmCitationWithUrlButNoDoi(): void
    {
        $citation = dataset_acm_citation([
            'title' => 'Online Data',
            'author' => 'Bob Wilson',
            'year' => '2024',
            'url' => 'https://example.com/data',
        ]);

        $this->assertSame(
            'Bob Wilson. 2024. Online Data. ASOG TBI Dataset Repository. https://example.com/data',
            $citation
        );
    }

    public function testAcmCitationDoiTakesPrecedenceOverUrl(): void
    {
        $citation = dataset_acm_citation([
            'title' => 'Dataset with Both',
            'author' => 'Alice Brown',
            'year' => '2023',
            'doi' => '10.5678/test',
            'url' => 'https://example.com/fallback',
        ]);

        $this->assertStringContainsString('https://doi.org/10.5678/test', $citation);
        $this->assertStringNotContainsString('https://example.com/fallback', $citation);
    }

    public function testAcmCitationNoParenthesesAroundYear(): void
    {
        $citation = dataset_acm_citation([
            'title' => 'Test Dataset',
            'author' => 'Test Author',
            'year' => '2026',
        ]);

        // ACM format: Author. Year. Title. Publisher. (no parentheses around year)
        $this->assertStringContainsString('Test Author. 2026.', $citation);
        $this->assertStringNotContainsString('Test Author. (2026).', $citation);
    }

    // ─── Contributor Fallback Tests ──────────────────────────────────────

    public function testApaCitationUsesContributorField(): void
    {
        $citation = dataset_apa_citation([
            'title' => 'Contributor Dataset',
            'contributor' => 'Contributor Name',
            'year' => '2026',
        ]);

        $this->assertStringStartsWith('Contributor Name.', $citation);
    }

    public function testMlaCitationUsesContributorField(): void
    {
        $citation = dataset_mla_citation([
            'title' => 'Contributor Dataset',
            'contributor' => 'Contributor Name',
            'year' => '2026',
        ]);

        $this->assertStringStartsWith('Contributor Name.', $citation);
    }

    public function testAcmCitationUsesContributorField(): void
    {
        $citation = dataset_acm_citation([
            'title' => 'Contributor Dataset',
            'contributor' => 'Contributor Name',
            'year' => '2026',
        ]);

        $this->assertStringStartsWith('Contributor Name.', $citation);
    }

    // ─── Missing Year Tests ──────────────────────────────────────────────

    public function testApaCitationDefaultsToCurrentYear(): void
    {
        $citation = dataset_apa_citation([
            'title' => 'No Year Dataset',
            'author' => 'Test Author',
        ]);

        $this->assertStringContainsString('(' . date('Y') . ')', $citation);
    }

    public function testMlaCitationDefaultsToCurrentYear(): void
    {
        $citation = dataset_mla_citation([
            'title' => 'No Year Dataset',
            'author' => 'Test Author',
        ]);

        $this->assertStringContainsString(', ' . date('Y') . '.', $citation);
    }

    public function testAcmCitationDefaultsToCurrentYear(): void
    {
        $citation = dataset_acm_citation([
            'title' => 'No Year Dataset',
            'author' => 'Test Author',
        ]);

        $this->assertStringContainsString('. ' . date('Y') . '.', $citation);
    }

    // ─── BibTeX Citation Tests ───────────────────────────────────────────

    public function testBibtexCitationBasic(): void
    {
        $bibtex = dataset_bibtex([
            'title' => 'Sample Dataset',
            'author' => 'John Smith',
            'year' => '2026',
        ]);

        $this->assertStringContainsString('@misc{', $bibtex);
        $this->assertStringContainsString('author = {John Smith}', $bibtex);
        $this->assertStringContainsString('title = {Sample Dataset}', $bibtex);
        $this->assertStringContainsString('year = {2026}', $bibtex);
        $this->assertStringContainsString('publisher = {ASOG TBI Dataset Repository}', $bibtex);
    }

    public function testBibtexCitationWithDoi(): void
    {
        $bibtex = dataset_bibtex([
            'title' => 'Research Data',
            'author' => 'Jane Doe',
            'year' => '2025',
            'doi' => '10.1234/example',
        ]);

        $this->assertStringContainsString('doi = {10.1234/example}', $bibtex);
    }

    public function testBibtexCitationWithUrlButNoDoi(): void
    {
        $bibtex = dataset_bibtex([
            'title' => 'Online Data',
            'author' => 'Bob Wilson',
            'year' => '2024',
            'url' => 'https://example.com/data',
        ]);

        $this->assertStringContainsString('url = {https://example.com/data}', $bibtex);
        $this->assertStringNotContainsString('doi', $bibtex);
    }

    public function testBibtexCitationDoiTakesPrecedenceOverUrl(): void
    {
        $bibtex = dataset_bibtex([
            'title' => 'Dataset with Both',
            'author' => 'Alice Brown',
            'year' => '2023',
            'doi' => '10.5678/test',
            'url' => 'https://example.com/fallback',
        ]);

        $this->assertStringContainsString('doi = {10.5678/test}', $bibtex);
        $this->assertStringNotContainsString('url', $bibtex);
    }

    public function testBibtexKeyGenerationFromTitle(): void
    {
        $bibtex = dataset_bibtex([
            'title' => 'My Cool Dataset!',
            'author' => 'Test Author',
            'year' => '2026',
        ]);

        // Key should be title without special chars + year
        $this->assertStringContainsString('@misc{MyCoolDataset2026,', $bibtex);
    }

    public function testBibtexWithoutAuthor(): void
    {
        $bibtex = dataset_bibtex([
            'title' => 'Anonymous Dataset',
            'author' => '',
            'year' => '2026',
        ]);

        $this->assertStringNotContainsString('author', $bibtex);
    }

    // ─── Cross-format Consistency Tests ──────────────────────────────────

    public function testAllFormatsUseSameDefaultAuthor(): void
    {
        $dataset = [
            'title' => 'Test Dataset',
            'author' => '',
            'year' => '2026',
        ];

        $apa = dataset_apa_citation($dataset);
        $mla = dataset_mla_citation($dataset);
        $acm = dataset_acm_citation($dataset);

        $this->assertStringStartsWith('ASOG TBI.', $apa);
        $this->assertStringStartsWith('ASOG TBI.', $mla);
        $this->assertStringStartsWith('ASOG TBI.', $acm);
    }

    public function testAllFormatsUseSameDefaultPublisher(): void
    {
        $dataset = [
            'title' => 'Test Dataset',
            'author' => 'Test Author',
            'year' => '2026',
        ];

        $apa = dataset_apa_citation($dataset);
        $mla = dataset_mla_citation($dataset);
        $acm = dataset_acm_citation($dataset);

        $this->assertStringContainsString('ASOG TBI Dataset Repository', $apa);
        $this->assertStringContainsString('ASOG TBI Dataset Repository', $mla);
        $this->assertStringContainsString('ASOG TBI Dataset Repository', $acm);
    }

    public function testAllFormatsIncludeYear(): void
    {
        $dataset = [
            'title' => 'Year Test',
            'author' => 'Year Author',
            'year' => '2025',
        ];

        $apa = dataset_apa_citation($dataset);
        $mla = dataset_mla_citation($dataset);
        $acm = dataset_acm_citation($dataset);

        $this->assertStringContainsString('2025', $apa);
        $this->assertStringContainsString('2025', $mla);
        $this->assertStringContainsString('2025', $acm);
    }

    public function testAllFormatsIncludeTitle(): void
    {
        $dataset = [
            'title' => 'Important Research Data',
            'author' => 'Research Author',
            'year' => '2026',
        ];

        $apa = dataset_apa_citation($dataset);
        $mla = dataset_mla_citation($dataset);
        $acm = dataset_acm_citation($dataset);

        $this->assertStringContainsString('Important Research Data', $apa);
        $this->assertStringContainsString('Important Research Data', $mla);
        $this->assertStringContainsString('Important Research Data', $acm);
    }
}

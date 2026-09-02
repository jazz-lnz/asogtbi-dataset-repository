<?php

use App\Models\DatasetModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class RapidMvpContractTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        helper(['citation', 'recommendation', 'dataset']);
    }

    public function testDatasetStatusLabelsMatchPublicSiteLifecycle(): void
    {
        $this->assertSame([
            DatasetModel::STATUS_PENDING_TECHNICAL => 'Pending Technical Review',
            DatasetModel::STATUS_TECHNICAL_REVISION => 'Technical Revision Requested',
            DatasetModel::STATUS_PENDING_ETHICS => 'Pending Ethics Review',
            DatasetModel::STATUS_ETHICS_REVISION => 'Ethics Revision Requested',
            DatasetModel::STATUS_AWAITING_PUBLICATION => 'Awaiting Publication',
            DatasetModel::STATUS_PUBLISHED => 'Published',
            DatasetModel::STATUS_ARCHIVED => 'Archived',
            DatasetModel::STATUS_REJECTED => 'Rejected',
        ], DatasetModel::statusLabels());
    }

    public function testNewSubmissionsStartWithTechnicalVerification(): void
    {
        $this->assertSame(DatasetModel::STATUS_PENDING_TECHNICAL, DatasetModel::STATUS_PENDING);
    }

    public function testAccessOptionsRemainStableAcrossPublicationWorkflow(): void
    {
        $this->assertSame([
            DatasetModel::ACCESS_PUBLIC => 'Public',
            DatasetModel::ACCESS_INSTITUTIONAL => 'Institutional',
            DatasetModel::ACCESS_RESTRICTED => 'Restricted',
            DatasetModel::ACCESS_PRIVATE => 'Private',
        ], DatasetModel::accessOptions());
    }

    public function testCitationUsesRepositoryPublisher(): void
    {
        $citation = dataset_citation([
            'title' => 'Sample Data',
            'author' => 'ASOG TBI',
            'year' => '2026',
        ]);

        $this->assertSame('ASOG TBI. (2026). Sample Data. ASOG TBI Dataset Repository.', $citation);
    }

    public function testRecommendationScorePrioritizesSemanticMetadata(): void
    {
        $current = [
            'title' => 'Startup Incubator Jobs',
            'category' => 'Analytics',
            'data_type' => 'Tabular',
            'file_format' => 'CSV',
            'tags' => 'incubator, startup, jobs',
            'description' => 'Startup incubation analytics dataset',
        ];
        $related = [
            'title' => 'Startup Funding and Jobs',
            'category' => 'Analytics',
            'data_type' => 'Tabular',
            'file_format' => 'CSV',
            'tags' => 'startup, funding, jobs',
            'description' => 'Startup analytics funding dataset',
        ];
        $unrelated = [
            'title' => 'Marine Biology Observations',
            'category' => 'Analytics',
            'data_type' => 'Tabular',
            'file_format' => 'CSV',
            'tags' => 'ocean, coral, fish',
            'description' => 'Coastal species observation collection',
        ];

        $relatedScore = metadata_similarity_score($current, $related);
        $unrelatedScore = metadata_similarity_score($current, $unrelated);

        $this->assertGreaterThan(50, $relatedScore);
        $this->assertGreaterThan($unrelatedScore, $relatedScore);
    }

    public function testGenericCategoryAndStorageMatchesDoNotImplyRelevance(): void
    {
        $score = metadata_similarity_score([
            'title' => 'Twitter Sentiment Analysis',
            'category' => 'Dummy Data',
            'data_type' => 'Tabular',
            'file_format' => 'ZIP',
            'tags' => 'twitter,sentiment',
            'description' => 'Public opinion from social posts',
        ], [
            'title' => 'Crop Yield Analysis',
            'category' => 'Dummy Data',
            'data_type' => 'Tabular',
            'file_format' => 'ZIP',
            'tags' => 'crop,yield',
            'description' => 'Farm harvest production figures',
        ]);

        $this->assertSame(10, $score);
    }

    public function testSearchRelevanceFindsRelatedMetadataWithoutAnExactPhrase(): void
    {
        $relatedScore = search_relevance_score('social engagement', [
            'title' => 'Social Media Trends',
            'tags' => 'social,media,trends',
            'category' => 'Communication',
            'description' => 'Patterns in social platform activity',
        ]);
        $unrelatedScore = search_relevance_score('social engagement', [
            'title' => 'Crop Yield Forecasts',
            'tags' => 'crop,farm,harvest',
            'category' => 'Agriculture',
            'description' => 'Seasonal production estimates',
        ]);

        $this->assertGreaterThan(0, $relatedScore);
        $this->assertSame(0, $unrelatedScore);
    }

    public function testRecommendationVocabularyConnectsKnownTopicFamilies(): void
    {
        $twitterTokens = recommendation_tokens('Twitter engagement');
        $cropTokens = recommendation_tokens('Crop productivity');

        $this->assertContains('social', $twitterTokens);
        $this->assertContains('media', $twitterTokens);
        $this->assertContains('agriculture', $cropTokens);
        $this->assertContains('farm', $cropTokens);
    }

    public function testDatasetCoverUsesPlaceholderUntilAnUploadExists(): void
    {
        $placeholder = dataset_cover_url(['id' => 42, 'cover_image' => null]);
        $uploaded = dataset_cover_url(['id' => 42, 'cover_image' => 'uploads/dataset-covers/42/cover.webp']);

        $this->assertStringEndsWith('/assets/img/placeholders/dataset-placeholder-img.png', $placeholder);
        $this->assertStringEndsWith('/datasets/42/cover', $uploaded);
    }
}

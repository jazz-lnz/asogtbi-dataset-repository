<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ThemeIsolationContractTest extends CIUnitTestCase
{
    public function testAboutStylesAreNotPartOfTheSharedApplicationBundle(): void
    {
        $applicationCss = file_get_contents(ROOTPATH . 'public/assets/css/app.css');
        $mainLayout = file_get_contents(ROOTPATH . 'app/Views/layouts/main.php');
        $portalLayout = file_get_contents(ROOTPATH . 'app/Views/layouts/portal.php');

        $this->assertIsString($applicationCss);
        $this->assertIsString($mainLayout);
        $this->assertIsString($portalLayout);
        $this->assertStringNotContainsString('@import url("./app/about.css")', $applicationCss);
        $this->assertStringContainsString('<?php if ($isAbout): ?>', $mainLayout);
        $this->assertStringContainsString("base_url('assets/css/app/about.css')", $mainLayout);
        $this->assertStringNotContainsString("base_url('assets/css/app/about.css')", $portalLayout);
    }

    public function testAboutThemeRulesFollowTheRootThemeAttribute(): void
    {
        $aboutCss = file_get_contents(ROOTPATH . 'public/assets/css/app/about.css');

        $this->assertIsString($aboutCss);
        $this->assertStringContainsString(':root:not([data-theme="dark"]) body', $aboutCss);
        $this->assertStringContainsString(':root[data-theme="dark"] body', $aboutCss);
        $this->assertStringNotContainsString('body:not([data-theme="dark"])', $aboutCss);
        $this->assertStringNotContainsString('body[data-theme="dark"]', $aboutCss);
        $this->assertStringContainsString('--headingColor: #ffffff;', $aboutCss);
        $this->assertMatchesRegularExpression(
            '/\\.stat-card\\s+\\.stat-value\\s*\\{\\s*color:\\s*var\\(--headingColor\\);/',
            $aboutCss,
        );
    }

    public function testBrowserColorSchemeOnlyFollowsAnExplicitDarkTheme(): void
    {
        $foundationCss = file_get_contents(ROOTPATH . 'public/assets/css/app/foundation.css');

        $this->assertIsString($foundationCss);
        $this->assertMatchesRegularExpression(
            '/:root\\s*\\{\\s*color-scheme:\\s*light;/',
            $foundationCss,
        );
        $this->assertMatchesRegularExpression(
            '/:root\\[data-theme="light"\\]\\s*\\{\\s*color-scheme:\\s*light;/',
            $foundationCss,
        );
        $this->assertMatchesRegularExpression(
            '/:root\\[data-theme="dark"\\]\\s*\\{\\s*color-scheme:\\s*dark;/',
            $foundationCss,
        );
        $this->assertStringNotContainsString('color-scheme: light dark;', $foundationCss);
    }
}

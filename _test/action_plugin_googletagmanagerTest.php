<?php

/**
 * @group plugin_googletagmanager
 * @group plugins
 */

class action_plugin_googletagmanagerTest extends \DokuWikiTest
{
    const gtmPluginName = 'googletagmanager';

    public function setUp(): void
    {
        $this->pluginsEnabled[] = self::gtmPluginName;
        parent::setUp();
    }

    public function test_google_tag_manager()
    {
        global $conf;
        $gtmValue = "GTM-12345";
        $conf['plugin'][self::gtmPluginName][action_plugin_googletagmanager::GTMID] = $gtmValue;

        $pageId = 'start';
        saveWikiText($pageId, "Content", 'Script Test base');
        idx_addPage($pageId);

        $request = new TestRequest();
        $response = $request->get(['id' => $pageId, '/doku.php']);

        /**
         * Tags to searched
         */
        $tagsSearched = ["script", "noscript"];

        foreach ($tagsSearched as $tagSearched) {
            $domElements = $response->queryHTML($tagSearched);

            $patternFound = false;
            foreach ($domElements as $domElement) {
                $patternFound = false;
                foreach ($domElements as $domElement) {
                    if ($tagSearched=="script") {
                        $value = $domElement->textContent;
                    } else {
                        // iframe src as subelement of noscript
                        $value = $domElement->firstChild->getAttribute("src");
                    }
                    $patternFound = preg_match("!https://www.googletagmanager.com/.*id=.*$gtmValue!s", $value) === 1;
                    if ($patternFound) {
                        break;
                    }
                }
                $this->assertTrue($patternFound, "The GTM scripts have not been found for the tag $tagSearched");
            }
        }
    }

    public function test_google_tag_manager_awcode()
    {
        global $conf;
        $gtmValue = "AW-12345";
        $conf['plugin'][self::gtmPluginName][action_plugin_googletagmanager::GTMID] = $gtmValue;

        $pageId = 'start';
        saveWikiText($pageId, "Content", 'Script Test base');
        idx_addPage($pageId);

        $request = new TestRequest();
        $response = $request->get(['id' => $pageId, '/doku.php']);

        $domElements = $response->queryHTML("script");

        $patternFound = false;
        foreach ($domElements as $domElement) {
            $value = $domElement->textContent;
            $patternFound = preg_match("/$gtmValue/", $value) === 1;
            if ($patternFound) {
                break;
            }
        }
        $this->assertTrue($patternFound, "The GTM scripts have not been found");
    }
}

<?php


class action_plugin_googletagmanagerTest extends DokuWikiTest
{

    const gtmPluginName = 'googletagmanager';

    public function setUp()
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
        $response = $request->get(array('id' => $pageId, '/doku.php'));

        /**
         * Tags to searched
         */
        $tagsSearched = ["script", "noscript"];

        foreach ($tagsSearched as $tagSearched) {

            $domElements = $response->queryHTML($tagSearched)->get();

            $patternFound = 0;
            foreach ($domElements as $domElement) {
                /**
                 * @var DOMElement $domElement
                 */
                if ($tagSearched=="script") {
                    $value = $domElement->textContent;
                } else {
                    // iframe src
                    $value = $domElement->firstChild->getAttribute("src");
                }
                $patternFound = preg_match("/$gtmValue/i", $value);
                if ($patternFound === 1) {
                    break;
                }
            }
            $this->assertEquals(1, $patternFound, "The GTM scripts have been found for the tag $tagSearched");
        }


    }

}

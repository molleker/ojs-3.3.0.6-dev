<?php

/**
 * @file tests/classes/core/CoreTest.inc.php
 *
 * Copyright (c) 2013-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class CoreTest
 * @ingroup tests_classes_core
 *
 * @see Core
 *
 * @brief Tests for the Core class.
 */

import('lib.pkp.tests.PKPTestCase');

class CoreTest extends PKPTestCase
{
    /**
     * Test Core::removeBaseUrl method using the default
     * base url config setting.
     *
     * @param string $baseUrl
     * @param string $url
     * @param string $expectUrl
     * @covers removeBaseUrl
     * @dataProvider testRemoveBaseUrlDataProvider
     */
    public function testRemoveBaseUrl($baseUrl, $url, $expectUrl)
    {
        $configData = & Config::getData();
        $configData['general']['base_url'] = $baseUrl;

        $actualUrl = Core::removeBaseUrl($url);
        $this->assertEquals($expectUrl, $actualUrl);
    }

    /**
     * Test Core::removeBaseUrl method using the base_url[...]
     * override config settings.
     *
     * @param string $contextPath
     * @param string $baseUrl
     * @param string $url
     * @param string $expectUrl
     * @covers removeBaseUrl
     * @dataProvider testRemoveBaseUrlOverrideDataProvider
     */
    public function testRemoveBaseUrlOverride($contextPath, $baseUrl, $url, $expectUrl)
    {
        $configData = & Config::getData();
        $configData['general']['base_url[' . $contextPath . ']'] = $baseUrl;
        $configData['general']['base_url[test2]'] = $baseUrl . '/test';
        // edge case: context matches a page
        $configData['general']['base_url[art]'] = $baseUrl . '/art';

        $actualUrl = Core::removeBaseUrl($url);
        $this->assertEquals($expectUrl, $actualUrl);
    }

    /**
     * Return cases data for testRemoveBaseUrl test.
     *
     * @return array
     */
    public function testRemoveBaseUrlDataProvider()
    {
        $cases = [];

        // Without host.
        $cases[] = ['http://localhost/ojs', '/', ''];
        $cases[] = ['http://localhost/ojs', '/index.php', ''];
        $cases[] = ['http://localhost/ojs', '/ojs', ''];
        $cases[] = ['http://localhost/ojs', '/ojs/index.php/ojs/index', '/ojs/index'];
        $cases[] = ['http://localhost/ojs', '/ojs/index.php/ojstest/index', '/ojstest/index'];
        // Without host and rewrite rules removing index.php.
        $cases[] = ['http://localhost/ojs', '/ojs/ojstest/index', '/ojstest/index'];

        // With host.
        $cases[] = ['http://localhost/ojs', 'http://localhost/ojs/', ''];
        $cases[] = ['http://localhost/ojs', 'http://localhost/ojs/index.php', ''];
        $cases[] = ['http://localhost/ojs', 'http://localhost/ojs/index.php/ojstest/index', '/ojstest/index'];
        $cases[] = ['http://localhost/ojs', 'http://localhost/ojs/index.php/ojstest/index/index/path?arg1=arg&arg2=arg', '/ojstest/index/index/path?arg1=arg&arg2=arg'];
        // With host and rewrite rules removing index.php.
        $cases[] = ['http://localhost/ojs', 'http://localhost/ojs/ojstest/index', '/ojstest/index'];

        // Path info disabled.
        $cases[] = ['http://localhost/ojs', 'http://localhost/ojs/index.php?journal=test', '?journal=test'];
        $cases[] = ['http://localhost/ojs', 'http://localhost/ojs', ''];
        $cases[] = ['http://localhost/ojs', 'http://localhost/ojs/index.php', ''];
        $cases[] = ['http://localhost/ojs', 'http://localhost/ojs/index.php?', '?'];
        // Path info disabled and rewrite rules removing index.php.
        $cases[] = ['http://localhost/ojs', 'http://localhost/ojs?journal=test', '?journal=test'];

        // Path info disabled without host.
        $cases[] = ['http://localhost/ojs', '/ojs/index.php?journal=test', '?journal=test'];
        $cases[] = ['http://localhost/ojs', '/ojs', ''];
        $cases[] = ['http://localhost/ojs', '/ojs/index.php', ''];
        $cases[] = ['http://localhost/ojs', '/ojs/index.php?', '?'];
        // Path info disabled without host and rewrite rules removing index.php.
        $cases[] = ['http://localhost/ojs', '/ojs?journal=test', '?journal=test'];

        // Edge cases
        $cases[] = ['http://localhost/ojs', 'http://localhost/ojs/index.php/art/article/view/100', '/art/article/view/100'];

        return $cases;
    }

    /**
     * Return cases data for testRemoveBaseUrl test.
     *
     * @return array
     */
    public function testRemoveBaseUrlOverrideDataProvider()
    {
        $cases = [];

        // Url without context or any other url component.
        $cases[] = ['test', 'http://localhost', '/', '/test'];
        $cases[] = ['test', 'http://localhost', '/?', '/test/?'];

        // Url without context or any other path.
        $cases[] = ['test', 'http://localhost', 'http://localhost', '/test'];
        $cases[] = ['test', 'http://localhost', 'http://localhost/?', '/test/?'];

        // Url without context removed by rewrite rules.
        $cases[] = ['test', 'http://localhost/ojs', '/ojs/index', '/test/index'];
        // Same as above but with index.php.
        $cases[] = ['test', 'http://localhost/ojs', '/ojs/index.php/index', '/test/index'];

        // Impossible to know which base url forms the url.
        $cases[] = ['test', 'http://localhost/ojstest', '/ojstest/test/index', false];
        // Same as above, but possible to know because of the 'index.php' presence.
        $cases[] = ['test', 'http://localhost/ojstest', '/ojstest/index.php/test/index', '/test/index'];

        // Overlaping contexts.
        $cases[] = ['test1', 'http://localhost', '/test/index', '/test2/index'];
        $cases[] = ['test1', 'http://localhost', '/test/index', '/test2/index'];
        // Overlaping contexts, path info disabled.
        $cases[] = ['test1', 'http://localhost', '/test/index.php?journal=test2&page=index', '/test2?journal=test2&page=index'];
        // Overlaping contexts, path info disabled, rewrite rules removing index.php.
        $cases[] = ['test1', 'http://localhost', '/test?journal=test2&page=index', '/test2?journal=test2&page=index'];

        // Path info disabled, overwrite rules removing index.php
        $cases[] = ['test', 'http://localhost/ojstest', '/ojstest?journal=test&page=index', '/test?journal=test&page=index'];
        // Path info disabled only.
        $cases[] = ['test', 'http://localhost/ojstest', '/ojstest/index.php?journal=test&page=index', '/test?journal=test&page=index'];

        // Edge cases
        $cases[] = ['art', 'http://localhost', 'http://localhost/art/article/view/100', '/art/article/view/100'];

        return $cases;
    }
}

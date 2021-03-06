/**
 * @file cypress/tests/integration/API.spec.js
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 */

describe('API tests', function() {
	it('Sets an API secret', function() {
		// Before API keys will function, an API key secret must be set in the configuration file.
		// This test is used to ensure one is set. (The default configuration file has an empty secret.)
		cy.readFile('config.inc.php').then((text) => {
			cy.writeFile('config.inc.php',
				text.replace("api_key_secret = \"\"", "api_key_secret = \"Api_Key_Secret_For_Testing_Purposes_Only\"")
			);
		});
	});
	it("Tries an API request without authorization", function() {
		cy.request({
			url: 'index.php/publicknowledge/api/v1/users',
			failOnStatusCode: false
		}).then(response => {
			expect(response.status).to.eq(403);
		});
	});

	it("Configures a manager's API key", function() {
		cy.login('dbarnes', null, 'publicknowledge');
		cy.get('.app__userNav button').click();
		cy.get('a:contains("Edit Profile")').click();
		cy.get('a[name="apiSettings"]').click();
		cy.get('input[id="apiKeyEnabled"]').check();
		cy.get('input[id="generateApiKey"]').check();
		cy.get('form[id="apiProfileForm"] button:contains("Save")').click();
		cy.waitJQuery();
		cy.get('span:contains("Your changes have been saved.")');
		cy.get('input[id^="apiKey-"]').invoke('val').as('apiKey').then(function() {
			cy.log(this.apiKey);
		});
		cy.logout();
	});
	it('Tries an API request with an API key', function() {
		// Ensure that an API request returns a 200 OK code.
		cy.request('index.php/publicknowledge/api/v1/users?apiToken=' + this.apiKey);
	});
})

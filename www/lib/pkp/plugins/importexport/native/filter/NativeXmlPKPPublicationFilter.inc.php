<?php

/**
 * @file plugins/importexport/native/filter/NativeXmlPKPPublicationFilter.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class NativeXmlPKPPublicationFilter
 * @ingroup plugins_importexport_native
 *
 * @brief Base class that converts a Native XML document to a set of publications
 */

use APP\facades\Repo;
use APP\publication\Publication;

import('lib.pkp.plugins.importexport.native.filter.NativeImportFilter');

class NativeXmlPKPPublicationFilter extends NativeImportFilter
{
    /**
     * Constructor
     *
     * @param FilterGroup $filterGroup
     */
    public function __construct($filterGroup)
    {
        $this->setDisplayName('Native XML publication import');
        parent::__construct($filterGroup);
    }


    //
    // Implement template methods from PersistableFilter
    //
    /**
     * @copydoc PersistableFilter::getClassName()
     */
    public function getClassName()
    {
        return 'lib.pkp.plugins.importexport.native.filter.NativeXmlPKPPublicationFilter';
    }


    //
    // Implement template methods from NativeImportFilter
    //
    /**
     * Return the plural element name
     *
     * @return string
     */
    public function getPluralElementName()
    {
        return 'publications';
    }

    /**
     * Get the singular element name
     *
     * @return string
     */
    public function getSingularElementName()
    {
        return 'publication';
    }

    /**
     * Handle a singular element import.
     *
     * @param DOMElement $node
     */
    public function handleElement($node)
    {
        $deployment = $this->getDeployment();
        $context = $deployment->getContext();

        $submission = $deployment->getSubmission();

        $publication = Repo::publication()->newDataObject();

        $publication->setData('submissionId', $submission->getId());

        $publication->stampModified();
        $publication = $this->populateObject($publication, $node);

        $publicationLocale = $node->getAttribute('locale');
        if (empty($publicationLocale)) {
            $publicationLocale = $context->getPrimaryLocale();
        }

        $publication->setData('locale', $publicationLocale);
        $publication->setData('version', $node->getAttribute('version'));
        $publication->setData('seq', $node->getAttribute('seq'));
        $publication->setData('accessStatus', $node->getAttribute('access_status'));
        $publication->setData('status', $node->getAttribute('status'));
        $publication->setData('primaryContactId', $node->getAttribute('primary_contact_id'));
        $publication->setData('urlPath', $node->getAttribute('url_path'));

        $publicationId = Repo::publication()->dao->insert($publication);
        $publication = Repo::publication()->get($publicationId);
        $deployment->setPublication($publication);

        for ($n = $node->firstChild; $n !== null; $n = $n->nextSibling) {
            if (is_a($n, 'DOMElement')) {
                $this->handleChildElement($n, $publication);
            }
        }

        Repo::publication()->dao->update($publication);

        return Repo::publication()->get($publication->getId());
    }

    /**
     * Populate the entity object from the node
     *
     * @param PKPPublication $publication
     * @param DOMElement $node
     *
     * @return Publication
     */
    public function populateObject($publication, $node)
    {
        if ($datePublished = $node->getAttribute('date_published')) {
            $publication->setData('datePublished', $datePublished);
        }

        return $publication;
    }

    /**
     * Handle an element whose parent is the publication element.
     *
     * @param DOMElement $n
     * @param PKPPublication $publication
     */
    public function handleChildElement($n, $publication)
    {
        $setterMappings = $this->_getLocalizedPublicationFields();
        $controlledVocabulariesMappings = $this->_getControlledVocabulariesMappings();

        [$locale, $value] = $this->parseLocalizedContent($n);
        if (empty($locale)) {
            $locale = $publication->getData('locale');
        }

        if (in_array($n->tagName, $setterMappings)) {
            $publication->setData($n->tagName, $value, $locale);
        } elseif (isset($controlledVocabulariesMappings[$n->tagName])) {
            $controlledVocabulariesDao = $submissionKeywordDao = DAORegistry::getDAO($controlledVocabulariesMappings[$n->tagName][0]);
            $insertFunction = $controlledVocabulariesMappings[$n->tagName][1];

            $controlledVocabulary = [];
            for ($nc = $n->firstChild; $nc !== null; $nc = $nc->nextSibling) {
                if (is_a($nc, 'DOMElement')) {
                    $controlledVocabulary[] = $nc->textContent;
                }
            }

            $controlledVocabulariesValues = [];
            $controlledVocabulariesValues[$locale] = $controlledVocabulary;

            $controlledVocabulariesDao->$insertFunction($controlledVocabulariesValues, $publication->getId(), false);

            $publicationNew = Repo::publication()->get($publication->getId());
            $publication->setData($n->tagName, $publicationNew->getData($n->tagName));
        } else {
            switch ($n->tagName) {
            // Otherwise, delegate to specific parsing code
            case 'id':
                $this->parseIdentifier($n, $publication);
                break;
            case 'authors':
                $this->parseAuthors($n, $publication);
                break;
            case 'citations':
                $this->parseCitations($n, $publication);
                break;
            case 'copyrightYear':
                $publication->setData('copyrightYear', $n->textContent);
                break;
            case 'licenseUrl':
                $publication->setData('licenseUrl', $n->textContent);
                break;
            default:
                $deployment = $this->getDeployment();
                $deployment->addWarning(ASSOC_TYPE_PUBLICATION, $publication->getId(), __('plugins.importexport.common.error.unknownElement', ['param' => $n->tagName]));
        }
        }
    }

    //
    // Element parsing
    //
    /**
     * Parse an identifier node and set up the publication object accordingly
     *
     * @param DOMElement $element
     * @param PKPPublication $publication
     */
    public function parseIdentifier($element, $publication)
    {
        $deployment = $this->getDeployment();
        $submission = $deployment->getSubmission();

        $advice = $element->getAttribute('advice');
        switch ($element->getAttribute('type')) {
            case 'internal':
                // "update" advice not supported yet.
                assert(!$advice || $advice == 'ignore');

                if ($element->textContent == $submission->getData('currentPublicationId')) {
                    $submission->setData('currentPublicationId', $publication->getId());
                    Repo::submission()->dao->update($submission);
                }

                break;
            case 'public':
                if ($advice == 'update') {
                    $publication->setData('pub-id::publisher-id', $element->textContent);
                }
                break;
            default:
                if ($advice == 'update') {
                    if ($element->getAttribute('type') == 'doi') {
                        if ($doiObject = $publication->getData('doiObject')) {
                            Repo::doi()->edit($doiObject, ['doi' => $element->textContent]);
                        } else {
                            $newDoiObject = Repo::doi()->newDataObject(
                                [
                                    'doi' => $element->textContent,
                                    'contextId' => $submission->getData('contextId')
                                ]
                            );
                            $doiId = Repo::doi()->add($newDoiObject);
                            $publication->setData('doiId', $doiId);
                        }
                    } else {
                        $pubIdPlugins = PluginRegistry::loadCategory('pubIds', true, $deployment->getContext()->getId());
                        $publication->setData('pub-id::' . $element->getAttribute('type'), $element->textContent);
                    }
                }
        }
    }

    /**
     * Parse an authors element
     *
     * @param DOMElement $node
     * @param PKPPublication $publication
     */
    public function parseAuthors($node, $publication)
    {
        for ($n = $node->firstChild; $n !== null; $n = $n->nextSibling) {
            if (is_a($n, 'DOMElement')) {
                assert($n->tagName == 'author');
                $this->parseAuthor($n, $publication);
            }
        }
    }

    /**
     * Parse an author and add it to the submission.
     *
     * @param DOMElement $n
     * @param Publication $publication
     */
    public function parseAuthor($n, $publication)
    {
        return $this->importWithXMLNode($n, 'native-xml=>author');
    }

    /**
     * Parse a publication citation and add it to the publication.
     *
     * @param DOMElement $n
     * @param PKPPublication $publication
     */
    public function parseCitations($n, $publication)
    {
        $publicationId = $publication->getId();
        $citationsString = '';
        foreach ($n->childNodes as $citNode) {
            $nodeText = trim($citNode->textContent);
            if (empty($nodeText)) {
                continue;
            }
            $citationsString .= $nodeText . "\n";
        }
        $publication->setData('citationsRaw', $citationsString);
        $citationDao = DAORegistry::getDAO('CitationDAO'); /** @var CitationDAO $citationDao */
        $citationDao->importCitations($publicationId, $citationsString);
    }

    //
    // Helper functions
    //
    /**
     * Get node name to setter function mapping for localized data.
     *
     * @return array
     */
    public function _getLocalizedPublicationFields()
    {
        return [
            'title',
            'prefix',
            'subtitle',
            'abstract',
            'coverage',
            'type',
            'source',
            'rights',
            'copyrightHolder',
        ];
    }

    /**
     * Get node name to DAO and insert function mapping.
     *
     * @return array
     */
    public function _getControlledVocabulariesMappings()
    {
        return [
            'keywords' => ['SubmissionKeywordDAO', 'insertKeywords'],
            'agencies' => ['SubmissionAgencyDAO', 'insertAgencies'],
            'languages' => ['SubmissionLanguageDAO', 'insertLanguages'],
            'disciplines' => ['SubmissionDisciplineDAO', 'insertDisciplines'],
            'subjects' => ['SubmissionSubjectDAO', 'insertSubjects'],
        ];
    }

    /**
     * Get the representation export filter group name
     *
     * @return string
     */
    public function getRepresentationExportFilterGroupName()
    {
        assert(false); // Subclasses must override
    }

    /**
     * Get the import filter for a given element.
     *
     * @param string $elementName Name of XML element
     *
     * @return Filter
     */
    public function getImportFilter($elementName)
    {
        assert(false); // Subclasses should override
    }
}

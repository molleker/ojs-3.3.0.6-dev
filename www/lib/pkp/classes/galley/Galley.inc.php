<?php

/**
 * @file classes/galley/Galley.inc.php
 *
 * Copyright (c) 2014-2022 Simon Fraser University
 * Copyright (c) 2003-2022 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class galley
 * @ingroup galley
 *
 * @brief A galley is a presentation version of a published submission file.
 */

namespace PKP\galley;

use APP\core\Application;

use APP\facades\Repo;
use PKP\facades\Locale;
use PKP\submission\Representation;
use PKP\submissionFile\SubmissionFile;

class Galley extends Representation
{
    public ?SubmissionFile $_submissionFile = null;

    //
    // Get/set methods
    //
    /**
     * Get views count.
     *
     * @return int
     */
    public function getViews()
    {
        $application = Application::get();
        $submissionFileID = $this->getData('submissionFileId');
        if ($submissionFileID) {
            return $application->getPrimaryMetricByAssoc(ASSOC_TYPE_SUBMISSION_FILE, $submissionFileID);
        } else {
            return 0;
        }
    }

    /**
     * Get label/title.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->getData('label');
    }

    /**
     * Set label/title.
     *
     * @param $label string
     */
    public function setLabel($label)
    {
        $this->setData('label', $label);
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->getData('locale');
    }

    /**
     * Set locale.
     *
     * @param $locale string
     */
    public function setLocale($locale)
    {
        $this->setData('locale', $locale);
    }

    /**
     * Return the "best" article ID -- If a public article ID is set,
     * use it; otherwise use the internal article Id.
     *
     * @return string
     */
    public function getBestGalleyId()
    {
        return $this->getData('urlPath')
            ? $this->getData('urlPath')
            : $this->getId();
    }

    /**
     * Get the submission file corresponding to this galley.
     *
     * @deprecated 3.3
     *
     * @return SubmissionFile
     */
    public function getFile()
    {
        if (!isset($this->_submissionFile) && $this->getData('submissionFileId')) {
            $this->_submissionFile = Repo::submissionFile()->get($this->getData('submissionFileId'));
        }
        return $this->_submissionFile;
    }

    /**
     * Get the file type corresponding to this galley.
     *
     * @deprecated 3.3
     *
     * @return string MIME type
     */
    public function getFileType()
    {
        $galleyFile = $this->getFile();
        return $galleyFile ? $galleyFile->getData('mimetype') : null;
    }

    /**
     * Determine whether the galley is a PDF.
     *
     * @deprecated 3.4
     *
     * @return boolean
     */
    public function isPdfGalley()
    {
        return $this->getFileType() == 'application/pdf';
    }

    /**
     * Get the localized galley label.
     *
     * @return string
     */
    public function getGalleyLabel()
    {
        $label = $this->getLabel();
        if ($this->getLocale() && $this->getLocale() != Locale::getLocale()) {
            $label .= ' (' . Locale::getMetadata($this->getLocale())->getDisplayName() . ')';
        }
        return $label;
    }

    /**
     * @see Representation::getName()
     *
     * This override exists to provide a functional getName() in order to make
     * native XML export work correctly.  It is only used in that single instance.
     *
     * @param $locale string unused, except to match the function prototype in Representation.
     *
     * @return array
     */
    public function getName($locale)
    {
        return [$this->getLocale() => $this->getLabel()];
    }

    /**
     * Override the parent class to fetch the non-localized label.
     *
     * @see Representation::getLocalizedName()
     *
     * @return string
     */
    public function getLocalizedName()
    {
        return $this->getLabel();
    }

    /**
     * @copydoc \PKP\submission\Representation::setStoredPubId()
     */
    public function setStoredPubId($pubIdType, $pubId)
    {
        if ($pubIdType == 'doi') {
            if ($doiObject = $this->getData('doiObject')) {
                Repo::doi()->edit($doiObject, ['doi' => $pubId]);
            } else {
                $newDoiObject = Repo::doi()->newDataObject(
                    [
                        'doi' => $pubId,
                        'contextId' => $this->getContextId()
                    ]
                );
                $doiId = Repo::doi()->add($newDoiObject);

                $this->setData('doiId', $doiId);
            }
        } else {
            parent::setStoredPubId($pubIdType, $pubId);
        }
    }
}

if (!PKP_STRICT_MODE) {
    // Required for import/export toolset
    class_alias('\PKP\galley\Galley', '\Galley');
}

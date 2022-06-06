<?php

/**
 * @file controllers/grid/files/query/QueryNoteFilesGridDataProvider.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class QueryNoteFilesGridDataProvider
 * @ingroup controllers_grid_files_query
 *
 * @brief Provide access to query files management.
 */

use APP\facades\Repo;
use Exception;

use PKP\security\authorization\QueryAccessPolicy;
use PKP\submissionFile\SubmissionFile;

import('lib.pkp.controllers.grid.files.SubmissionFilesGridDataProvider');

class QueryNoteFilesGridDataProvider extends SubmissionFilesGridDataProvider
{
    /** @var int Note ID */
    public $_noteId;

    /**
     * Constructor
     *
     * @param int $noteId Note ID
     */
    public function __construct($noteId)
    {
        parent::__construct(SubmissionFile::SUBMISSION_FILE_QUERY);
        $this->_noteId = $noteId;
    }

    //
    // Overridden public methods from FilesGridDataProvider
    //
    /**
     * @copydoc GridDataProvider::getAuthorizationPolicy()
     */
    public function getAuthorizationPolicy($request, $args, $roleAssignments)
    {
        $this->setUploaderRoles($roleAssignments);

        return new QueryAccessPolicy($request, $args, $roleAssignments, $this->getStageId());
    }

    /**
     * @copydoc FilesGridDataProvider::getSelectAction()
     */
    public function getSelectAction($request)
    {
        $query = $this->getAuthorizedContextObject(ASSOC_TYPE_QUERY);
        import('lib.pkp.controllers.grid.files.fileList.linkAction.SelectFilesLinkAction');
        return new SelectFilesLinkAction(
            $request,
            $this->getRequestArgs(),
            __('editor.submission.selectFiles')
        );
    }

    /**
     * @copydoc GridDataProvider::loadData()
     */
    public function loadData($filter = [])
    {
        // Retrieve all submission files for the given file query.
        $submission = $this->getSubmission();
        $query = $this->getAuthorizedContextObject(ASSOC_TYPE_QUERY);

        $noteDao = DAORegistry::getDAO('NoteDAO'); /** @var NoteDAO $noteDao */
        $note = $noteDao->getById($this->_noteId);
        if ($note->getAssocType() != ASSOC_TYPE_QUERY || $note->getAssocId() != $query->getId()) {
            throw new Exception('Invalid note ID specified!');
        }

        $collector = Repo::submissionFile()
            ->getCollector()
            ->filterByAssoc(
                ASSOC_TYPE_NOTE,
                [$this->_noteId]
            )->filterBySubmissionIds([$submission->getId()])
            ->filterByFileStages([(int) $this->getFileStage()]);
        $submissionFilesIterator = Repo::submissionFile()->getMany($collector);

        return $this->prepareSubmissionFileData(iterator_to_array($submissionFilesIterator), $this->_viewableOnly, $filter);
    }

    /**
     * @copydoc GridDataProvider::getRequestArgs()
     */
    public function getRequestArgs()
    {
        $query = $this->getAuthorizedContextObject(ASSOC_TYPE_QUERY);
        $representation = $this->getAuthorizedContextObject(ASSOC_TYPE_REPRESENTATION);
        return array_merge(
            parent::getRequestArgs(),
            [
                'assocType' => ASSOC_TYPE_NOTE,
                'assocId' => $this->_noteId,
                'queryId' => $query->getId(),
                'noteId' => $this->_noteId,
                'representationId' => $representation ? $representation->getId() : null,
            ]
        );
    }

    /**
     * @copydoc FilesGridDataProvider::getAddFileAction()
     */
    public function getAddFileAction($request)
    {
        $submission = $this->getSubmission();
        $query = $this->getAuthorizedContextObject(ASSOC_TYPE_QUERY);
        import('lib.pkp.controllers.api.file.linkAction.AddFileLinkAction');
        return new AddFileLinkAction(
            $request,
            $submission->getId(),
            $this->getStageId(),
            $this->getUploaderRoles(),
            $this->getFileStage(),
            ASSOC_TYPE_NOTE,
            $this->_noteId,
            null,
            null,
            null,
            $query->getId()
        );
    }
}

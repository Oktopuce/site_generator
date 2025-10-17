<?php

declare(strict_types=1);

namespace Oktopuce\SiteGenerator\EventListener;

use TYPO3\CMS\Backend\Hooks\DataHandlerAuthenticationContext;
use TYPO3\CMS\Backend\Security\SudoMode\Access\AccessSubjectInterface;
use TYPO3\CMS\Backend\Security\SudoMode\Access\TableAccessSubject;
use TYPO3\CMS\Backend\Security\SudoMode\Event\SudoModeRequiredEvent;

/** Skip verification for DtataHandler operations on be_groups table
 *  This is a patch to avoid a verification error when creating a new be_group
 *
 * @see : https://forge.typo3.org/issues/107752
 */
final class SkipBeGroupSudoModeRequired
{
    public function __invoke(SudoModeRequiredEvent $event): void
    {
        $claim = $event->getClaim();

        // Abort if it's not for site_generator
        if ($claim->instruction->getUri()->getPath() !== '/typo3/wizard/sitegenerator') {
            return;
        }

        // Ensure the event context matches DataHandler operations
        if ($event->getClaim()->origin !== DataHandlerAuthenticationContext::class) {
            return;
        }

        // Filter for TableAccessSubject types only
        $tableAccessSubjects = array_filter(
            $event->getClaim()->subjects,
            static fn(AccessSubjectInterface $subject): bool => $subject instanceof TableAccessSubject,
        );

        // Abort if there are unhandled subject types
        if ($event->getClaim()->subjects !== $tableAccessSubjects) {
            return;
        }

        /** @var list<TableAccessSubject> $tableAccessSubjects */
        foreach ($tableAccessSubjects as $subject) {
            // Expecting format: tableName.fieldName.id
            if (substr_count($subject->getSubject(), '.') !== 2) {
                return;
            }

            [$tableName, $fieldName, $id] = explode('.', $subject->getSubject());

            // Only handle be_groups table
            if ($tableName !== 'be_groups') {
                return;
            }
        }

        // All conditions met — disable verification
        $event->setVerificationRequired(false);
    }
}

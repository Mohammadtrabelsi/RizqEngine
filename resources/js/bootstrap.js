/**
 * Application bootstrap.
 *
 * Historically this file wired up Popper.js and the Bootstrap 4 jQuery
 * plugin. The UI has been migrated to Tailwind CSS, so those dependencies
 * were removed. jQuery is still loaded from the CDN in the authenticated
 * layout for the third-party DataTables / perfect-scrollbar plugins, and
 * the interactive widgets that CoreUI used to provide (dropdowns, modals,
 * collapsible navigation, tooltips) are now handled in `app.js`.
 */

version 1.6.0
=============
**Date:** 28-Jan-2015

- (enh #27): Romanian translation added.
- (bug #28): Revert #20 Undo fix for switch inputs addressed now by plugin upgrade.
- (enh #29): Russian translation added.

version 1.5.0
=============
**Date:** 12-Jan-2015

- (bug #23): Fix HTML5 Input type initialization.
- (bug #24): Fix undefined class constant 'self::INPUT_RADIO'.
- (bug #25): Fix namespaces in use of Html and Config helpers.
- Code formatting updates as per Yii2 standards.
- Revamp to use new Krajee base TranslationTrait.

version 1.4.0
=============
**Date:** 06-Dec-2014

- bug #16: Correct method for validating input widget using `\kartik\base\Config`.
- bug #20: Reinitialize Switch Inputs in detail view edit mode.

version 1.3.0
=============
**Date:** 10-Nov-2014

- PSR4 alias change
- Set dependency on Krajee base components
- Better validation of Krajee input widgets 
- Set release to stable
- Delete button default option enhancements

version 1.2.0
=============
**Date:** 19-Oct-2014

- enh #13: Improve hide of elements and remove fade delay at initialization
- enh #14: Add various container properties to configure HTML options
- enh #15: Refactor and optimize client code

version 1.1.0
=============
**Date:** 15-Jul-2014

- enh #10: Added animation to fade out between view and edit modes
- PSR4 alias change

version 1.0.0
=============
**Date:** 15-May-2014

- Initial release
- enh #1: Changed `static` variable references to `self` (kartik-v)
- enh #4: Added confirmation message management (lestat1968)
- enh #4: Added Italian language translations (lestat1968)
- enh #8: Added Hungarian language translations (monghuz)
- Added support for more inputs
  - `DetailView::INPUT_DATE_RANGE` or `\kartik\widgets\DateRangePicker`
  - `DetailView::INPUT_SORTABLE` or `\kartik\sortinput\SortableInput`
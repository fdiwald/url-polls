<?php
namespace Url_Polls;

const PLUGIN_NAME = 'url-polls';
const LANG_DOMAIN = PLUGIN_NAME;
const VERSION = '1.0.0';

const POST_TYPE_POLL = 'url-polls_poll';

const META_RECIPIENTS = 'url-polls_poll_recipients';

const MENU_SETTINGS = 'url-polls_settings';

const SETTINGS_SECTION_GENERAL = 'url-polls_general';
const SETTING_DEFAULT_RECIPIENTS = 'url-polls_default_recipients';

const ACTION_ADD_DEFAULT_RECIPIENTS = 'add_default_recipients';
const ACTION_DELETE_RECIPIENT = 'delete_recipient';
const ACTION_BULK_DELETE = 'bulk-delete';
const ACTION_BULK_ACCEPT = 'bulk-accept';
const ACTION_BULK_REJECT = 'bulk-reject';
const ACTION_EXPORT = 'export';

const ANSWER_UNANSWERED = 0;
const ANSWER_ACCEPT = 1;
const ANSWER_REJECT = 2;
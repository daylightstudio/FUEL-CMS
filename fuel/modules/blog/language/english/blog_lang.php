<?php
/*
|--------------------------------------------------------------------------
| Module Names
|--------------------------------------------------------------------------
*/
$lang['module_blog_posts'] = 'Posts';
$lang['module_blog_categories'] = 'Categories';
$lang['module_blog_comments'] = 'Comments';
$lang['module_blog_links'] = 'Links';
$lang['module_blog_authors'] = 'Authors';
$lang['module_blog_settings'] = 'Settings';

/*
|--------------------------------------------------------------------------
| Errors
|--------------------------------------------------------------------------
*/
$lang['blog_error_blank_comment'] = 'Please enter in a comment.';
$lang['blog_error_invalid_comment_email'] = 'Please enter in a valid email address.';
$lang['blog_error_comment_site_submit'] = 'Comments must be submitted through the form on the website.';
$lang['blog_error_comment_already_submitted'] = 'This comment has already been submitted.';
$lang['blog_error_consecutive_comments'] = 'Please wait to post consecutive comments.';
$lang['blog_error_delete_uncategorized'] = 'You cannot delete the Uncategorized category.';

/*
|--------------------------------------------------------------------------
| Page Titles
|--------------------------------------------------------------------------
*/
$lang['blog_archives_page_title'] = 'Archives';
$lang['blog_authors_list_page_title'] = 'Authors';
$lang['blog_author_posts_page_title'] = '%s Posts';
$lang['blog_categories_page_title'] = 'Categories';
$lang['blog_search_page_title'] = '%s Search Results';

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/
$lang['blog_page_num_title'] = 'Posts %1s-%2s';
$lang['blog_prev_page'] = '&lt;&lt; Previous Page';
$lang['blog_next_page'] = 'Next Page &gt;&gt;';
$lang['blog_first_link'] = '';
$lang['blog_last_link'] = '';


$lang['blog_error_no_posts_to_comment'] = 'There are no posts to comment on.';
$lang['blog_post_is_not_published'] = 'The post associated with this comment is not published and so no notifications will be sent on comment replies.';
$lang['blog_comment_notify_option1'] = 'All';
$lang['blog_comment_notify_option2'] = 'Commentor';
$lang['blog_comment_notify_option3'] = 'None';



/***************************************************************************
IMPORTANT: SEVERAL FORM FIELD LABELS ALREADY EXIST IN THE fuel language file
***************************************************************************/

/*
|--------------------------------------------------------------------------
| Posts (several fields are in the main form_label_ common)
|--------------------------------------------------------------------------
*/
$lang['form_label_formatting'] = 'Formatting';
$lang['form_label_author'] = 'Author';
$lang['form_label_sticky'] = 'Sticky';
$lang['form_label_allow_comments'] = 'Allow Comments';
$lang['form_label_categories'] = 'Categories';


/*
|--------------------------------------------------------------------------
| Comments 
|--------------------------------------------------------------------------
*/
$lang['blog_comment_monitor_subject']= "%s: A comment has been added.";
$lang['blog_comment_monitor_msg']= "A comment has been added to your blog post. To review the comment login to FUEL:";
$lang['blog_comment_reply_subject']= "%1s Blog Comment Reply";
$lang['blog_comment_reply_msg']= "%1s has replied to your comment on the article %2s.";

$lang['blog_captcha_text'] = "Enter the text you see in the image in the form field above. <br />If you cannot read the text, refresh the page.";

$lang['blog_comment_is_spam'] = "The comment posted cannot be submitted as is because it was flagged as spam.";
$lang['blog_error_captcha_mismatch'] = "The text you inputted for the image did not match.";

$lang['blog_email_flagged_as_spam'] = 'FLAGGED AS SPAM!!!';
$lang['blog_email_published'] = 'Published';
$lang['blog_email_author_name'] = 'Author Name';
$lang['blog_email_author_email'] = 'Author Email';
$lang['blog_email_author_website'] = 'Website';
$lang['blog_email_author_ip'] = 'Author IP';
$lang['blog_email_content'] = 'Content';
$lang['form_label_post_title'] = 'Post Title';
$lang['form_label_comment'] = 'Comment';
$lang['form_label_comment_author_name'] = 'Comment Author Name';
$lang['form_label_is_spam'] = 'Is Spam';
$lang['form_label_post_published'] = 'Post Published';
$lang['form_label_date_submitted'] = 'Date Submitted';
$lang['form_label_ip_host'] = 'IP/Host';
$lang['form_label_replies'] = 'Replies';
$lang['form_label_reply'] = 'Reply';
$lang['form_label_commentor'] = 'Commentor';
$lang['form_label_reply_notify'] = 'Notify';
$lang['form_label_author_ip'] = 'Author IP Address';


/*
|--------------------------------------------------------------------------
| Settings 
|--------------------------------------------------------------------------
*/
$lang['form_label_uri'] = 'URI';
$lang['form_label_theme_path'] = 'Theme Location';
$lang['form_label_theme_layout'] = 'Theme Layout';
$lang['form_label_theme_module'] = 'Theme module';
$lang['form_label_use_cache'] = 'Use Cache';
$lang['form_label_allow_comments'] = 'Allow Comments';
$lang['form_label_monitor_comments'] = 'Monitor Comments';
$lang['form_label_use_captchas'] = 'Use Captchas';
$lang['form_label_save_spam'] = 'Save Spam';
$lang['form_label_akismet_api_key'] = 'Akismet Key';
$lang['form_label_multiple_comment_submission_time_limit'] = 'Comment Submission Time Limit';
$lang['form_label_multiple_comment_submission_time_limit_after_html'] = ' (in seconds)';
$lang['form_label_comments_time_limit'] = 'Allow comments for how long';
$lang['form_label_comments_time_limit_after_html'] = ' after post date (in days)';
$lang['form_label_cache_ttl'] = 'Cache Time to Live';
$lang['form_label_asset_upload_path'] = 'Asset Upload Path';
$lang['form_label_per_page'] = 'Per Page';
$lang['form_label_page_title_separator'] = 'Page Title Separator';
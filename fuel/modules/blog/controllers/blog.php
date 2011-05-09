<?php
require_once(MODULES_PATH.'/blog/libraries/Blog_base_controller.php');
class Blog extends Blog_base_controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	function _remap()
	{
		$year = ($this->uri->rsegment(2) != 'index') ? (int) $this->uri->rsegment(2) : NULL;
		$month = (int) $this->uri->rsegment(3);
		$day = (int) $this->uri->rsegment(4);
		$permalink = $this->uri->rsegment(5);
		$limit = (int) $this->fuel_blog->settings('per_page');
		
		$view_by = 'page';
		
		// we empty out year variable if it is page because we won't be querying on year'
		if (is_int($year) && !empty($year) && empty($permalink))
		{
			$view_by = 'date';
		}
		// if the first segment is id then treat the second segment as the id
		else if ($this->uri->rsegment(2) === 'id' && $this->uri->rsegment(3))
		{
			$view_by = 'permalink';
			$permalink = (int) $this->uri->rsegment(3);
		}
		else if (!empty($permalink))
		{
			$view_by = 'permalink';
		}
		
		// set this to false so that we can use segments for the limit
		$cache_id = fuel_cache_id();
		$cache = $this->fuel_blog->get_cache($cache_id);
		
		if (!empty($cache))
		{
			$output =& $cache;
		}
		else
		{
			$vars = $this->_common_vars();
			
			if ($view_by == 'permalink')
			{
				return $this->post($permalink);
			}
			else if ($view_by == 'date')
			{
				$page_title_arr = array();
				$posts_date = mktime(0, 0, 0, $month, $day, $year);
				if (!empty($day)) $page_title_arr[] = $day;
				if (!empty($month)) $page_title_arr[] = date('M', strtotime($posts_date));
				if (!empty($year)) $page_title_arr[] = $year;
				$vars['page_title'] = $page_title_arr;
				$vars['posts'] = $this->fuel_blog->get_posts_by_date($year, (int) $month, $day, $permalink, $limit);
				$vars['pagination'] = '';
			}
			else
			{
				$limit = $this->fuel_blog->settings('per_page');
				$this->load->library('pagination');
				$config['uri_segment'] = 3;
				$offset = $this->uri->segment($config['uri_segment']);
				$this->config->set_item('enable_query_strings', FALSE);
				$config['base_url'] = $this->fuel_blog->url('page/');
				$config['total_rows'] = count($this->fuel_blog->get_posts());
				$config['page_query_string'] = FALSE;
				$config['per_page'] = $limit;
				$config['num_links'] = 2;
				$config['prev_link'] = lang('blog_prev_page');
				$config['next_link'] = lang('blog_next_page');
				$config['first_link'] = lang('blog_first_link');
				$config['last_link'] = lang('blog_last_link');;
				
				$this->pagination->initialize($config); 
				
				if (!empty($offset))
				{
					$vars['page_title'] = lang('blog_page_num_title', $offset, $offset + $limit);
				}
				else
				{
					$vars['page_title'] = '';
				}
				$vars['posts'] = $this->fuel_blog->get_posts_by_page($limit, $offset);
				$vars['pagination'] = $this->pagination->create_links();
			}
			// show the index page if the page doesn't have any uri_segment(3)'
			$view = ($this->uri->rsegment(2) == 'index' OR ($this->uri->rsegment(2) == 'page' AND !$this->uri->segment(3))) ? 'index' : 'posts';
			$output = $this->_render($view, $vars, TRUE);
			$this->fuel_blog->save_cache($cache_id, $output);
		}
		
		$this->output->set_output($output);
	}
	
	function post($permalink = null)
	{
		if (empty($permalink)) show_404();
		$this->load->library('session');
		$blog_config = $this->config->item('blog');

		$post = $this->fuel_blog->get_post($permalink);

		if (isset($post->id))
		{
		
			$vars = $this->_common_vars();
			$vars['post'] = $post;
			$vars['user'] = $this->fuel_blog->logged_in_user();
			$vars['page_title'] = $post->title;
			$vars['next'] = $this->fuel_blog->get_next_post($post);
			$vars['prev'] = $this->fuel_blog->get_prev_post($post);
			
			$antispam = md5(random_string('unique'));
			
			$field_values = array();
			
			// post comment
			if (!empty($_POST))
			{
				$field_values = $_POST;
				
				// the id of "content" is a likely ID on the front end, so we use comment_content and need to remap
				$field_values['content'] = $field_values['new_comment'];
				unset($field_values['antispam']);
				
				if (!empty($_POST['new_comment']))
				{
					$vars['processed'] = $this->_process_comment($post);
				}
				else
				{
					add_error(lang('blog_error_blank_comment'));
				}
			}
			
			$cache_id = fuel_cache_id();
			$cache = $this->fuel_blog->get_cache($cache_id);
			if (!empty($cache) AND empty($_POST))
			{
				$output =& $cache;
			}
			else
			{
				$this->load->library('form');
				
				if (is_true_val($this->fuel_blog->settings('use_captchas')))
				{
					$captcha = $this->_render_captcha();
					$vars['captcha'] = $captcha;
				}
				$vars['thanks'] = ($this->session->flashdata('thanks')) ? blog_block('comment_thanks', $vars, TRUE) : '';
				$vars['comment_form'] = '';
				$this->session->set_userdata('antispam', $antispam);
				
				if (is_true_val($this->fuel_blog->settings('allow_comments')))
				{
					$this->load->module_model(BLOG_FOLDER, 'blog_comments_model');
					$this->load->library('form_builder', $blog_config['comment_form']);
					
					$fields['author_name'] = array('label' => 'Name', 'required' => TRUE);
					$fields['author_email'] = array('label' => 'Email', 'required' => TRUE);
					$fields['author_website'] = array('label' => 'Website');
					$fields['new_comment'] = array('label' => 'Comment', 'type' => 'textarea', 'required' => TRUE);
					$fields['post_id'] = array('type' => 'hidden', 'value' => $post->id);
					$fields['antispam'] = array('type' => 'hidden', 'value' => $antispam);
					if (!empty($vars['captcha']))
					{
						$fields['captcha'] = array('required' => TRUE, 'label' => 'Security Text', 'value' => '', 'after_html' => ' <span class="captcha">'.$vars['captcha']['image'].'</span><br /><span class="captcha_text">'.lang('blog_captcha_text').'</span>');
					}
					
					// now merge with config... can't do array_merge_recursive'
					foreach($blog_config['comment_form']['fields'] as $key => $field)
					{
						if (isset($fields[$key])) $fields[$key] = array_merge($fields[$key], $field);
					}

					if (!isset($blog_config['comment_form']['label_layout'])) $this->form_builder->label_layout = 'left';
					if (!isset($blog_config['comment_form']['submit_value'])) $this->form_builder->submit_value = 'Submit Comment';
					if (!isset($blog_config['comment_form']['use_form_tag'])) $this->form_builder->use_form_tag = TRUE;
					if (!isset($blog_config['comment_form']['display_errors'])) $this->form_builder->display_errors = TRUE;
					$this->form_builder->form_attrs = 'method="post" action="'.site_url($this->uri->uri_string()).'#comments_form"';
					$this->form_builder->set_fields($fields);
					$this->form_builder->set_field_values($field_values);
					$this->form_builder->set_validator($this->blog_comments_model->get_validation());
					$vars['comment_form'] = $this->form_builder->render();
					$vars['fields'] = $fields;
				}
				
				$output = $this->_render('post', $vars, TRUE);
				
				// save cache only if we are not posting data
				if (!empty($_POST)) 
				{
					$this->fuel_blog->save_cache($cache_id, $output);
				}
			}
			if (!empty($output))
			{
				$this->output->set_output($output);
				return;
			}
		}
		else
		{
			show_404();
		}
	}
	function _process_comment($post)
	{
		if (!is_true_val($this->fuel_blog->settings('allow_comments'))) return;
		
		$notified = FALSE;
		
		// check captcha
		if (!$this->_is_valid_captcha())
		{
			add_error(lang('blog_error_captcha_mismatch'));
		}
		
		// check that the site is submitted via the websit
		if (!$this->_is_site_submitted())
		{
			add_error(lang('blog_error_comment_site_submit'));
		}
		
		// check consecutive posts
		if (!$this->_is_not_consecutive_post())
		{
			add_error(lang('blog_error_consecutive_comments'));
		}
		
		$this->load->module_model(FUEL_FOLDER, 'users_model');
		
		$user = $this->users_model->find_one(array('email' => $this->input->post('author_email', TRUE)));

		// create comment
		$this->load->module_model(BLOG_FOLDER, 'blog_comments_model');
		$comment = $this->blog_comments_model->create();
		$comment->post_id = $post->id;
		
		$comment->author_id = (!empty($user->id)) ? $user->id : NULL;
		$comment->author_name = $this->input->post('author_name', TRUE);
		$comment->author_email = $this->input->post('author_email', TRUE);
		$comment->author_website = $this->input->post('author_website', TRUE);
		$comment->author_ip = $_SERVER['REMOTE_ADDR'];
		$comment->content = trim($this->input->post('new_comment', TRUE));
		$comment->date_added = NULL; // will automatically be added

		//http://googleblog.blogspot.com/2005/01/preventing-comment-spam.html
		//http://en.wikipedia.org/wiki/Spam_in_blogs

		// check double posts by IP address
		if ($comment->is_duplicate())
		{
			add_error(lang('blog_error_comment_already_submitted'));
		}
		
		// if no errors from above then proceed to submit
		if (!has_errors())
		{
			// submit to akisment for validity
			$comment = $this->_process_akismet($comment);

			// process links and add no follow attribute
			$comment = $this->_filter_comment($comment);

			// set published status
			if (is_true_val($comment->is_spam) OR $this->fuel_blog->settings('monitor_comments'))
			{
				$comment->published = 'no';
			}
			
			// save comment if saveable and redirect
			if (!is_true_val($comment->is_spam) OR (is_true_val($comment->is_spam) AND $this->fuel_blog->settings('save_spam')))
			{
				if ($comment->save())
				{
					$notified = $this->_notify($comment, $post);
					$this->load->library('session');
					$vars['post'] = $post;
					$vars['comment'] = $comment;
					$this->session->set_flashdata('thanks', TRUE);
					$this->session->set_userdata('last_comment_ip', $_SERVER['REMOTE_ADDR']);
					$this->session->set_userdata('last_comment_time', time());
					redirect($post->url);
				}
				else
				{
					add_errors($comment->errors());
				}
			}
			else
			{
				add_error(lang('blog_comment_is_spam'));
			}
		}
		return $notified;
	}
	
	// check captcha validity
	function _is_valid_captcha()
	{
		$valid = TRUE;
		
		// check captcha
		if (is_true_val($this->fuel_blog->settings('use_captchas')))
		{
			if (!$this->input->post('captcha') OR strtoupper($this->input->post('captcha')) != strtoupper($this->session->userdata('comment_captcha')))
			{
				$valid = FALSE;
			}
		}
		return $valid;
	}
	
	// check to make sure the site issued a session variable to check against
	function _is_site_submitted()
	{
		return ($this->session->userdata('antispam') AND $this->input->post('antispam') == $this->session->userdata('antispam'));
	}
	
	// disallow multiple successive submissions 
	function _is_not_consecutive_post()
	{
		$valid = TRUE;
		
		$time_exp_secs = $this->fuel_blog->settings('multiple_comment_submission_time_limit');
		$last_comment_time = ($this->session->userdata('last_comment_time')) ? $this->session->userdata('last_comment_time') : 0;
		$last_comment_ip = ($this->session->userdata('last_comment_ip')) ? $this->session->userdata('last_comment_ip') : 0;
		if ($_SERVER['REMOTE_ADDR'] == $last_comment_ip AND !empty($time_exp_secs))
		{
			if (time() - $last_comment_time < $time_exp_secs)
			{
				$valid = FALSE;
			}
		}
		return $valid;
	}

	// process through akisment
	function _process_akismet($comment)
	{
		if ($this->fuel_blog->settings('akismet_api_key'))
		{
			$this->load->module_library(BLOG_FOLDER, 'akismet');

			$akisment_comment = array(
				'author'	=> $comment->author_name,
				'email'		=> $comment->author_email,
				'body'		=> $comment->content
			);

			$config = array(
				'blog_url' => $this->fuel_blog->url(),
				'api_key' => $this->fuel_blog->settings('akismet_api_key'),
				'comment' => $akisment_comment
			);

			$this->akismet->init($config);

			if ( $this->akismet->errors_exist() )
			{				
				if ( $this->akismet->is_error('AKISMET_INVALID_KEY') )
				{
					log_error('AKISMET :: Theres a problem with the api key');
				}
				elseif ( $this->akismet->is_error('AKISMET_RESPONSE_FAILED') )
				{
					log_error('AKISMET :: Looks like the servers not responding');
				}
				elseif ( $this->akismet->is_error('AKISMET_SERVER_NOT_FOUND') )
				{
					log_error('AKISMET :: Wheres the server gone?');
				}
			}
			else
			{
				$comment->is_spam = ($this->akismet->is_spam()) ? 'yes' : 'no';
			}
		}
		
		return $comment;
	}
	
	// strip out 
	function _filter_comment($comment)
	{
		$this->load->helper('security');
		$comment_attrs = array('content', 'author_name', 'author_email', 'author_website');
		foreach($comment_attrs as $filter)
		{
			$text = $comment->$filter;
			
			// first remove any nofollow attributes to clean up... not perfect but good enough
			$text = preg_replace('/<a(.+)rel=["\'](.+)["\'](.+)>/Umi', '<a$1rel="nofollow"$3>', $text);
//			$text = str_replace('<a ', '<a rel="nofollow"', $text);
			
			$text = strip_image_tags($text);
			
			$comment->$filter = $text;
		}
		return $comment;
	}
	
	function _notify($comment, $post)
	{
		// send email to post author
		if (!empty($post->author))
		{
			$this->load->library('email');

			$this->email->from($this->config->item('from_email', 'fuel'), $this->config->item('site_name', 'fuel'));
			$this->email->to($post->author->email); 
			$this->email->subject(lang('blog_comment_monitor_subject', $this->fuel_blog->settings('title')));

			$msg = lang('blog_comment_monitor_msg');
			$msg .= "\n".fuel_url('blog/comments/edit/'.$comment->id)."\n\n";

			$msg .= (is_true_val($comment->is_spam)) ? lang('blog_email_flagged_as_spam')."\n" : '';
			$msg .= lang('blog_email_published').": ".$comment->published."\n";
			$msg .= lang('blog_email_author_name').": ".$comment->author_name."\n";
			$msg .= lang('blog_email_author_email').": ".$comment->author_email."\n";
			$msg .= lang('blog_email_author_website').": ".$comment->author_website."\n";
			$msg .= lang('blog_email_author_ip').": ".gethostbyaddr($comment->author_ip)." (".$comment->author_ip.")\n";
			$msg .= lang('blog_email_content').": ".$comment->content."\n";

			$this->email->message($msg);

			return $this->email->send();
		}
		else
		{
			return FALSE;
		}
	}
	
	function _render_captcha()
	{
		$this->load->library('captcha');
		$blog_config = $this->config->item('blog');
		$assets_folders = $this->config->item('assets_folders');
		$blog_folder = MODULES_PATH.BLOG_FOLDER.'/';
		$captcha_path = $blog_folder.'assets/captchas/';
		$word = strtoupper(random_string('alnum', 5));
		
		$captcha_options = array(
						'word'		 => $word,
						'img_path'	 => $captcha_path, // system path to the image
						'img_url'	 => captcha_path('', BLOG_FOLDER), // web path to the image
						'font_path'	 => $blog_folder.'fonts/',
					);
		$captcha_options = array_merge($captcha_options, $blog_config['captcha']);
		if (!empty($_POST['captcha']) AND $this->session->userdata('comment_captcha') == $this->input->post('captcha'))
		{
			$captcha_options['word'] = $this->input->post('captcha');
		}
		$captcha = $this->captcha->get_captcha_image($captcha_options);
		
		$this->session->set_userdata('comment_captcha', $captcha['word']);
		
		return $captcha;
	}
	

}
<?php
/**
 * Plugin Name: CMS Email Notifications
 * Description: Double opt-in newsletter subscriptions and queued new Job/News alerts.
 * Version: 1.0.0
 * Author: CMS-JOB-DESIGN team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CMS_Email_Notifications {
	const SUBSCRIBER_TYPE = 'cms_email_subscriber';
	const QUEUE_TYPE      = 'cms_email_queue';
	const REST_NAMESPACE  = 'cms-email/v1';
	const TOKEN_LIFETIME  = 172800;
	const BATCH_SIZE      = 20;

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_types' ) );
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_script' ) );
		add_action( 'transition_post_status', array( __CLASS__, 'on_publish' ), 20, 3 );
		add_action( 'cms_email_notifications_process_queue', array( __CLASS__, 'process_queue' ) );
		add_action( 'admin_post_nopriv_cms_email_action', array( __CLASS__, 'handle_email_action' ) );
		add_action( 'admin_post_cms_email_action', array( __CLASS__, 'handle_email_action' ) );
		add_action( 'template_redirect', array( __CLASS__, 'render_email_action' ) );
	}

	public static function register_types() {
		foreach ( array( self::SUBSCRIBER_TYPE, self::QUEUE_TYPE ) as $type ) {
			register_post_type( $type, array(
				'public'              => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_ui'             => false,
				'show_in_rest'        => false,
				'supports'            => array( 'title' ),
			) );
		}
	}

	public static function register_routes() {
		register_rest_route( self::REST_NAMESPACE, '/subscribe', array(
			'methods'             => 'POST',
			'callback'            => array( __CLASS__, 'subscribe' ),
			'permission_callback' => '__return_true',
			'args'                => array(
				'email'    => array( 'required' => true, 'sanitize_callback' => 'sanitize_email' ),
				'honeypot' => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
			),
		) );
	}

	public static function enqueue_script() {
		$path = __DIR__ . '/assets/cms-email-notifications.js';
		if ( ! file_exists( $path ) ) {
			return;
		}
		wp_enqueue_script( 'cms-email-notifications', WPMU_PLUGIN_URL . '/assets/cms-email-notifications.js', array(), '1.0.0', true );
		wp_localize_script( 'cms-email-notifications', 'cmsEmailNotifications', array(
			'endpoint' => esc_url_raw( rest_url( self::REST_NAMESPACE . '/subscribe' ) ),
			'nonce'    => wp_create_nonce( 'wp_rest' ),
			'messages' => array(
				'pending' => __( 'If this address can be subscribed, check its inbox for a confirmation link. No updates are sent until you confirm.', 'jobscout' ),
				'error'   => __( 'We could not process your request. Please try again later.', 'jobscout' ),
			),
		) );
	}

	public static function subscribe( WP_REST_Request $request ) {
		$email = strtolower( trim( (string) $request->get_param( 'email' ) ) );
		if ( $request->get_param( 'honeypot' ) || ! is_email( $email ) ) {
			return new WP_Error( 'invalid_subscription', __( 'Please enter a valid email address.', 'jobscout' ), array( 'status' => 400 ) );
		}

		$ip_hash = hash_hmac( 'sha256', (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ), wp_salt( 'auth' ) );
		$rate_key = 'cms_email_rate_' . substr( $ip_hash, 0, 32 );
		$count = (int) get_transient( $rate_key );
		if ( $count >= 5 ) {
			return new WP_REST_Response( array( 'message' => self::generic_message() ), 202 );
		}
		set_transient( $rate_key, $count + 1, HOUR_IN_SECONDS );

		$lock_name = 'cms_email_lock_' . hash( 'sha256', $email );
		$lock_time = (int) get_option( $lock_name, 0 );
		if ( $lock_time && $lock_time < time() - 120 ) {
			delete_option( $lock_name );
		}
		if ( ! add_option( $lock_name, time(), '', false ) ) {
			return new WP_REST_Response( array( 'message' => self::generic_message() ), 202 );
		}

		try {
			$subscriber = self::find_subscriber( $email );
			if ( $subscriber ) {
				if ( 'active' === get_post_meta( $subscriber->ID, '_cms_email_state', true ) ) {
					return new WP_REST_Response( array( 'message' => self::generic_message() ), 202 );
				}
				$last_sent = (int) get_post_meta( $subscriber->ID, '_cms_email_confirmation_sent', true );
				if ( time() - $last_sent < 300 ) {
					return new WP_REST_Response( array( 'message' => self::generic_message() ), 202 );
				}
			} else {
				$id = wp_insert_post( array(
					'post_type'   => self::SUBSCRIBER_TYPE,
					'post_status' => 'private',
					'post_title'  => 'Subscriber ' . substr( hash( 'sha256', $email ), 0, 16 ),
				), true );
				if ( is_wp_error( $id ) ) {
					return new WP_Error( 'subscription_storage_error', self::generic_message(), array( 'status' => 500 ) );
				}
				$subscriber = get_post( $id );
				update_post_meta( $id, '_cms_email_address', $email );
				update_post_meta( $id, '_cms_email_state', 'pending' );
				update_post_meta( $id, '_cms_email_created', time() );
			}

			update_post_meta( $subscriber->ID, '_cms_email_state', 'pending' );
			$token = self::new_token();
			update_post_meta( $subscriber->ID, '_cms_email_confirm_hash', self::token_hash( $token ) );
			update_post_meta( $subscriber->ID, '_cms_email_confirm_expires', time() + self::TOKEN_LIFETIME );
			update_post_meta( $subscriber->ID, '_cms_email_confirmation_sent', time() );
			$manage_token = self::manage_token( $subscriber->ID );
		} finally {
			delete_option( $lock_name );
		}
		$url = add_query_arg( array( 'cms_email_action' => 'confirm', 'token' => rawurlencode( $token ) ), home_url( '/' ) );
		$manage_url = add_query_arg( array( 'cms_email_action' => 'manage', 'sid' => $subscriber->ID, 'token' => rawurlencode( $manage_token ) ), home_url( '/' ) );
		$sent = self::send_html( $email, __( 'Confirm your email preferences', 'jobscout' ), self::confirmation_email( $url, $manage_url ) );
		if ( ! $sent ) {
			return new WP_Error( 'email_unavailable', __( 'We could not send a confirmation message right now. Please try again later.', 'jobscout' ), array( 'status' => 503 ) );
		}

		return new WP_REST_Response( array( 'message' => self::generic_message() ), 202 );
	}

	private static function generic_message() {
		return __( 'If this address can be subscribed, check its inbox for a confirmation link. No updates are sent until you confirm.', 'jobscout' );
	}

	private static function find_subscriber( $email ) {
		$posts = get_posts( array(
			'post_type'      => self::SUBSCRIBER_TYPE,
			'post_status'    => 'private',
			'posts_per_page' => 1,
			'fields'         => 'all',
			'meta_key'       => '_cms_email_address',
			'meta_value'     => $email,
			'no_found_rows'  => true,
		) );
		return $posts ? $posts[0] : null;
	}

	private static function new_token() {
		return rtrim( strtr( base64_encode( random_bytes( 32 ) ), '+/', '-_' ), '=' );
	}

	private static function token_hash( $token ) {
		return hash_hmac( 'sha256', (string) $token, wp_salt( 'auth' ) );
	}

	private static function manage_token( $subscriber_id ) {
		$email = (string) get_post_meta( $subscriber_id, '_cms_email_address', true );
		return rtrim( strtr( base64_encode( hash_hmac( 'sha256', 'manage|' . absint( $subscriber_id ) . '|' . $email, wp_salt( 'auth' ), true ) ), '+/', '-_' ), '=' );
	}

	public static function render_email_action() {
		$action = isset( $_GET['cms_email_action'] ) ? sanitize_key( wp_unslash( $_GET['cms_email_action'] ) ) : '';
		if ( ! in_array( $action, array( 'confirm', 'manage' ), true ) ) {
			return;
		}
		$token = isset( $_GET['token'] ) ? sanitize_text_field( wp_unslash( $_GET['token'] ) ) : '';
		$sid = isset( $_GET['sid'] ) ? absint( $_GET['sid'] ) : 0;
		$subscriber = self::find_by_token( $action, $token, $sid );
		status_header( 200 );
		nocache_headers();
		header( 'Referrer-Policy: no-referrer' );
		?><!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?php esc_html_e( 'Email preferences', 'jobscout' ); ?></title></head><body class="cms-email-action-page"><main style="max-width:620px;margin:8vh auto;padding:32px;background:#fff;font:16px/1.6 Arial,sans-serif;color:#352b28;box-shadow:0 4px 24px #0001"><h1 style="color:#ef741b"><?php esc_html_e( 'Email preferences', 'jobscout' ); ?></h1><?php
		if ( ! $subscriber ) {
			echo '<p>' . esc_html__( 'This link is invalid or has expired. Please submit the newsletter form again.', 'jobscout' ) . '</p>';
		} else {
			$nonce_action = 'cms_email_' . $action . '_' . $subscriber->ID;
			if ( 'confirm' === $action ) {
				?><p><?php esc_html_e( 'Choose which updates you would like to receive.', 'jobscout' ); ?></p><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="cms_email_action"><input type="hidden" name="cms_action" value="confirm"><input type="hidden" name="subscriber" value="<?php echo esc_attr( $subscriber->ID ); ?>"><input type="hidden" name="token" value="<?php echo esc_attr( $token ); ?>"><?php wp_nonce_field( $nonce_action ); ?><p><label><input type="checkbox" name="jobs" value="1" checked> <?php esc_html_e( 'New job alerts', 'jobscout' ); ?></label></p><p><label><input type="checkbox" name="news" value="1" checked> <?php esc_html_e( 'New articles', 'jobscout' ); ?></label></p><button style="background:#ef741b;color:#fff;border:0;padding:12px 22px;cursor:pointer" type="submit"><?php esc_html_e( 'Confirm subscription', 'jobscout' ); ?></button></form><?php
			} else {
				?><p><?php esc_html_e( 'Use the options below to update or stop these email notifications.', 'jobscout' ); ?></p><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="cms_email_action"><input type="hidden" name="cms_action" value="preferences"><input type="hidden" name="subscriber" value="<?php echo esc_attr( $subscriber->ID ); ?>"><input type="hidden" name="token" value="<?php echo esc_attr( $token ); ?>"><?php wp_nonce_field( $nonce_action ); ?><p><label><input type="checkbox" name="jobs" value="1" <?php checked( get_post_meta( $subscriber->ID, '_cms_email_jobs', true ), '1' ); ?>> <?php esc_html_e( 'New job alerts', 'jobscout' ); ?></label></p><p><label><input type="checkbox" name="news" value="1" <?php checked( get_post_meta( $subscriber->ID, '_cms_email_news', true ), '1' ); ?>> <?php esc_html_e( 'New articles', 'jobscout' ); ?></label></p><button style="background:#ef741b;color:#fff;border:0;padding:12px 22px;cursor:pointer" type="submit"><?php esc_html_e( 'Save preferences', 'jobscout' ); ?></button> <button name="cms_action" value="unsubscribe" style="background:#fff;color:#352b28;border:1px solid #aaa;padding:11px 18px;cursor:pointer" type="submit"><?php esc_html_e( 'Unsubscribe', 'jobscout' ); ?></button></form><?php
			}
		}
		?></main></body></html><?php
		exit;
	}

	private static function find_by_token( $action, $token, $subscriber_id = 0 ) {
		if ( ! preg_match( '/^[A-Za-z0-9_-]{40,50}$/', $token ) ) {
			return null;
		}
		if ( 'manage' === $action ) {
			$post = get_post( absint( $subscriber_id ) );
			if ( ! $post || self::SUBSCRIBER_TYPE !== $post->post_type || ! hash_equals( self::manage_token( $post->ID ), $token ) ) {
				return null;
			}
			return $post;
		}
		$key = '_cms_email_confirm_hash';
		$posts = get_posts( array(
			'post_type' => self::SUBSCRIBER_TYPE, 'post_status' => 'private', 'posts_per_page' => 2,
			'meta_key' => $key, 'meta_value' => self::token_hash( $token ), 'no_found_rows' => true,
		) );
		if ( 1 !== count( $posts ) ) {
			return null;
		}
		$post = $posts[0];
		if ( 'confirm' === $action && (int) get_post_meta( $post->ID, '_cms_email_confirm_expires', true ) < time() ) {
			return null;
		}
		return $post;
	}

	public static function handle_email_action() {
		$action = isset( $_POST['cms_action'] ) ? sanitize_key( wp_unslash( $_POST['cms_action'] ) ) : '';
		$id = isset( $_POST['subscriber'] ) ? absint( $_POST['subscriber'] ) : 0;
		$token = isset( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : '';
		$subscriber = get_post( $id );
		$valid_action = in_array( $action, array( 'confirm', 'preferences', 'unsubscribe' ), true );
		$token_action = 'confirm' === $action ? 'confirm' : 'manage';
		$verified = self::find_by_token( $token_action, $token, 'manage' === $token_action ? $id : 0 );
		if ( ! $valid_action || ! $subscriber || self::SUBSCRIBER_TYPE !== $subscriber->post_type || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ?? '' ) ), 'cms_email_' . $token_action . '_' . $id ) || ! $verified || (int) $subscriber->ID !== (int) $verified->ID ) {
			wp_die( esc_html__( 'This email link is invalid or has expired.', 'jobscout' ), '', array( 'response' => 403 ) );
		}

		if ( 'unsubscribe' === $action ) {
			update_post_meta( $id, '_cms_email_state', 'unsubscribed' );
			update_post_meta( $id, '_cms_email_jobs', '0' );
			update_post_meta( $id, '_cms_email_news', '0' );
			self::render_result( __( 'You have been unsubscribed.', 'jobscout' ) );
		}

		update_post_meta( $id, '_cms_email_jobs', isset( $_POST['jobs'] ) ? '1' : '0' );
		update_post_meta( $id, '_cms_email_news', isset( $_POST['news'] ) ? '1' : '0' );
		if ( 'confirm' === $action ) {
			if ( ! isset( $_POST['jobs'], $_POST['news'] ) ) {
				wp_die( esc_html__( 'Choose at least one update type.', 'jobscout' ), '', array( 'response' => 400 ) );
			}
			update_post_meta( $id, '_cms_email_state', 'active' );
			delete_post_meta( $id, '_cms_email_confirm_hash' );
			delete_post_meta( $id, '_cms_email_confirm_expires' );
			self::render_result( __( 'Your subscription is confirmed.', 'jobscout' ) );
		}
		self::render_result( __( 'Your email preferences have been saved.', 'jobscout' ) );
	}

	private static function render_result( $message ) {
		status_header( 200 );
		nocache_headers();
		header( 'Referrer-Policy: no-referrer' );
		?><!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?php esc_html_e( 'Email preferences', 'jobscout' ); ?></title></head><body><main style="max-width:620px;margin:8vh auto;padding:32px;background:#fff;font:16px/1.6 Arial,sans-serif;color:#352b28"><h1 style="color:#ef741b"><?php echo esc_html( $message ); ?></h1><p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return to the website', 'jobscout' ); ?></a></p></main></body></html><?php
		exit;
	}

	public static function on_publish( $new_status, $old_status, $post ) {
		if ( 'publish' !== $new_status || 'publish' === $old_status || wp_is_post_revision( $post->ID ) || wp_is_post_autosave( $post->ID ) ) {
			return;
		}
		$type = 'job_listing' === $post->post_type ? 'job' : ( 'post' === $post->post_type ? 'news' : '' );
		if ( ! $type || ( 'job' === $type && 'expired' === $new_status ) ) {
			return;
		}
		// Historical demo content is already on the site; never enqueue it on the first run.
		$published = strtotime( get_gmt_from_date( $post->post_date ) . ' UTC' );
		if ( $published && $published < time() - 30 * DAY_IN_SECONDS ) {
			update_post_meta( $post->ID, '_cms_email_alert_skipped_old', '1' );
			return;
		}
		$event_key = $type . ':' . $post->ID;
		$existing = get_posts( array( 'post_type' => self::QUEUE_TYPE, 'post_status' => 'private', 'posts_per_page' => 1, 'meta_key' => '_cms_email_event_key', 'meta_value' => $event_key, 'fields' => 'ids' ) );
		if ( $existing ) {
			return;
		}
		$id = wp_insert_post( array( 'post_type' => self::QUEUE_TYPE, 'post_status' => 'private', 'post_title' => $event_key ), true );
		if ( is_wp_error( $id ) ) {
			return;
		}
		update_post_meta( $id, '_cms_email_event_key', $event_key );
		update_post_meta( $id, '_cms_email_event_type', $type );
		update_post_meta( $id, '_cms_email_source_id', $post->ID );
		update_post_meta( $id, '_cms_email_queue_state', 'queued' );
		if ( ! wp_next_scheduled( 'cms_email_notifications_process_queue' ) ) {
			wp_schedule_single_event( time() + 15, 'cms_email_notifications_process_queue' );
		}
	}

	public static function process_queue() {
		$stale = get_posts( array( 'post_type' => self::QUEUE_TYPE, 'post_status' => 'private', 'posts_per_page' => 20, 'meta_key' => '_cms_email_queue_state', 'meta_value' => 'processing', 'no_found_rows' => true ) );
		foreach ( $stale as $stale_item ) {
			$started = (int) get_post_meta( $stale_item->ID, '_cms_email_processing_started', true );
			if ( ! $started || $started < time() - 900 ) {
				update_post_meta( $stale_item->ID, '_cms_email_queue_state', 'queued' );
			}
		}
		$items = get_posts( array( 'post_type' => self::QUEUE_TYPE, 'post_status' => 'private', 'posts_per_page' => self::BATCH_SIZE, 'orderby' => 'date', 'order' => 'ASC', 'meta_key' => '_cms_email_queue_state', 'meta_value' => 'queued', 'no_found_rows' => true ) );
		foreach ( $items as $item ) {
			$source_id = absint( get_post_meta( $item->ID, '_cms_email_source_id', true ) );
			$type = get_post_meta( $item->ID, '_cms_email_event_type', true );
			$source = get_post( $source_id );
			if ( ! $source || 'publish' !== $source->post_status || ! in_array( $type, array( 'job', 'news' ), true ) ) {
				update_post_meta( $item->ID, '_cms_email_queue_state', 'skipped' );
				continue;
			}
			update_post_meta( $item->ID, '_cms_email_queue_state', 'processing' );
			update_post_meta( $item->ID, '_cms_email_processing_started', time() );
			$subscribers = get_posts( array( 'post_type' => self::SUBSCRIBER_TYPE, 'post_status' => 'private', 'posts_per_page' => self::BATCH_SIZE, 'offset' => (int) get_post_meta( $item->ID, '_cms_email_offset', true ), 'meta_key' => '_cms_email_state', 'meta_value' => 'active', 'orderby' => 'ID', 'order' => 'ASC', 'no_found_rows' => true ) );
			foreach ( $subscribers as $subscriber ) {
				$offset = (int) get_post_meta( $item->ID, '_cms_email_offset', true );
				if ( '1' !== get_post_meta( $subscriber->ID, '_cms_email_' . ( 'job' === $type ? 'jobs' : 'news' ), true ) ) {
					update_post_meta( $item->ID, '_cms_email_offset', $offset + 1 );
					continue;
				}
				$receipt_key = '_cms_email_sent_' . md5( (string) get_post_meta( $item->ID, '_cms_email_event_key', true ) );
				if ( get_post_meta( $subscriber->ID, $receipt_key, true ) ) {
					update_post_meta( $item->ID, '_cms_email_offset', $offset + 1 );
					continue;
				}
				if ( 'active' !== get_post_meta( $subscriber->ID, '_cms_email_state', true ) || '1' !== get_post_meta( $subscriber->ID, '_cms_email_' . ( 'job' === $type ? 'jobs' : 'news' ), true ) ) {
					update_post_meta( $item->ID, '_cms_email_offset', $offset + 1 );
					continue;
				}
				$email = (string) get_post_meta( $subscriber->ID, '_cms_email_address', true );
				$manage_token = self::manage_token( $subscriber->ID );
				$manage_url = add_query_arg( array( 'cms_email_action' => 'manage', 'sid' => $subscriber->ID, 'token' => rawurlencode( $manage_token ) ), home_url( '/' ) );
				$message = 'job' === $type ? self::job_email( $source, $manage_url ) : self::news_email( $source, $manage_url );
				if ( self::send_html( $email, 'job' === $type ? sprintf( __( 'New job: %s', 'jobscout' ), get_the_title( $source ) ) : sprintf( __( 'New article: %s', 'jobscout' ), get_the_title( $source ) ), $message ) ) {
					update_post_meta( $subscriber->ID, $receipt_key, time() );
					update_post_meta( $item->ID, '_cms_email_offset', $offset + 1 );
				} else {
					update_post_meta( $item->ID, '_cms_email_queue_state', 'queued' );
					wp_schedule_single_event( time() + 300, 'cms_email_notifications_process_queue' );
					break;
				}
			}
			$processed = (int) get_post_meta( $item->ID, '_cms_email_offset', true );
			$total = self::active_subscriber_count();
			if ( $processed < $total ) {
				update_post_meta( $item->ID, '_cms_email_queue_state', 'queued' );
				wp_schedule_single_event( time() + 60, 'cms_email_notifications_process_queue' );
			} else {
				update_post_meta( $item->ID, '_cms_email_queue_state', 'complete' );
				delete_post_meta( $item->ID, '_cms_email_processing_started' );
			}
		}
		if ( $items && ! wp_next_scheduled( 'cms_email_notifications_process_queue' ) && self::has_queued_items() ) {
			wp_schedule_single_event( time() + 60, 'cms_email_notifications_process_queue' );
		}
	}

	private static function active_subscriber_count() {
		$query = new WP_Query( array( 'post_type' => self::SUBSCRIBER_TYPE, 'post_status' => 'private', 'posts_per_page' => 1, 'meta_key' => '_cms_email_state', 'meta_value' => 'active', 'fields' => 'ids' ) );
		return (int) $query->found_posts;
	}

	private static function has_queued_items() {
		$items = get_posts( array( 'post_type' => self::QUEUE_TYPE, 'post_status' => 'private', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_cms_email_queue_state', 'meta_value' => 'queued' ) );
		return (bool) $items;
	}

	private static function send_html( $to, $subject, $html ) {
		return (bool) wp_mail( $to, wp_strip_all_tags( $subject ), $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
	}

	private static function shell( $content, $reason, $manage_url = '' ) {
		$site = esc_html( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
		$reason = $reason ? $reason : __( 'You receive this message because you confirmed these email updates.', 'jobscout' );
		$manage = $manage_url ? '<p style="font-size:12px;color:#777">' . esc_html( $reason ) . ' <a href="' . esc_url( $manage_url ) . '">' . esc_html__( 'Manage preferences or unsubscribe', 'jobscout' ) . '</a>.</p>' : '<p style="font-size:12px;color:#777">' . esc_html( $reason ) . '</p>';
		return '<!doctype html><html><body style="margin:0;background:#f3f3f3;font-family:Arial,sans-serif;color:#382e2a"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f3f3"><tr><td align="center" style="padding:24px 10px"><table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;background:#fff"><tr><td style="background:#ef741b;padding:18px 24px;color:#fff;font-size:20px;font-weight:bold">' . $site . '</td></tr><tr><td style="padding:28px 24px">' . $content . '</td></tr><tr><td style="padding:16px 24px;border-top:1px solid #eee">' . $manage . '</td></tr></table></td></tr></table></body></html>';
	}

	private static function button( $label, $url ) {
		return '<p style="margin:24px 0"><a href="' . esc_url( $url ) . '" style="background:#ef741b;color:#fff;text-decoration:none;padding:13px 20px;display:inline-block;font-weight:bold">' . esc_html( $label ) . '</a></p>';
	}

	private static function confirmation_email( $url, $manage_url ) {
		$content = '<h1 style="font-size:23px">' . esc_html__( 'Confirm your email', 'jobscout' ) . '</h1><p>' . esc_html__( 'Confirm your address and choose whether to receive job alerts, new articles, or both. This link expires in 48 hours.', 'jobscout' ) . '</p>' . self::button( __( 'CONFIRM EMAIL', 'jobscout' ), $url ) . '<p style="font-size:12px;color:#777">' . esc_html__( 'If you did not request this, you can ignore this message.', 'jobscout' ) . '</p>';
		return self::shell( $content, __( 'This message was sent because an email subscription was requested. It will not become active unless you confirm.', 'jobscout' ), $manage_url );
	}

	private static function job_email( $job, $manage_url ) {
		$company = get_post_meta( $job->ID, '_company_name', true );
		$location = get_post_meta( $job->ID, '_job_location', true );
		$types = wp_get_post_terms( $job->ID, 'job_listing_type', array( 'fields' => 'names' ) );
		$type = ! is_wp_error( $types ) && $types ? implode( ', ', $types ) : '';
		$excerpt = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $job->post_content ) ), 38 );
		$content = '<h1 style="font-size:22px">' . esc_html( get_the_title( $job ) ) . '</h1><p>' . esc_html( implode( ' · ', array_filter( array( $company, $location, $type ) ) ) ) . '</p><p>' . esc_html( $excerpt ) . '</p>' . self::button( __( 'VIEW JOB DETAILS', 'jobscout' ), get_permalink( $job ) );
		return self::shell( $content, '', $manage_url );
	}

	private static function news_email( $post, $manage_url ) {
		$image = get_the_post_thumbnail_url( $post, 'large' );
		$content = '<h1 style="font-size:22px">' . esc_html( get_the_title( $post ) ) . '</h1>';
		if ( $image ) {
			$content .= '<p><img src="' . esc_url( $image ) . '" alt="" style="display:block;width:100%;max-width:552px;height:auto"></p>';
		}
		$content .= '<p>' . esc_html( wp_trim_words( get_the_excerpt( $post ) ?: wp_strip_all_tags( $post->post_content ), 45 ) ) . '</p>' . self::button( __( 'READ MORE', 'jobscout' ), get_permalink( $post ) );
		return self::shell( $content, '', $manage_url );
	}
}

CMS_Email_Notifications::init();

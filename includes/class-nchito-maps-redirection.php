<?php
/**
 * The main plugin class
 *
 * @link              https://mwale.me
 * @since             1.0.0
 * @package           Nchito_Maps_Redirection
 */

declare(strict_types=1);
namespace MwaleMe\Nchito_Maps_Redirection;

/**
 * The class responsible for running the integration
 */
class Nchito_Maps_Redirection {
	/**
	 * The WP username
	 *
	 * @var string $username The WP account's username.
	 */
	private string $username;
	/**
	 * The App password
	 *
	 * @var string $password The WP App password.
	 */
	private string $password;
	/**
	 * The site url
	 *
	 * @var string $url The url of the site running Redirection.
	 */
	private string $url;

	/**
	 * Initializes an object's properties upon creation of the object.
	 *
	 * @param string $username The WordPress account's username.
	 * @param string $password The WordPress Application password.
	 * @param string $url The url of the site running Redirection.
	 */
	public function __construct(
		string $username,
		string $password,
		string $url
	) {
		$this->username = $username;
		$this->password = $password;
		$this->url      = $url;

		add_action( 'user_register', array( $this, 'user_register_action' ), 10, 2 );
		add_filter( 'wp_new_user_notification_email', array( $this, 'get_new_user_email' ), 10, 3 );
		add_action(
			'rest_api_init',
			function () {
				register_rest_route(
					'nchito-maps/v1',
					'/maps',
					array(
						'methods'  => 'GET',
						'callback' => array( $this, 'create_maps_redirect' ),
					)
				);
			}
		);
	}

	/**
	 * Creates a new redirect via endpoints exposed by the Redirection plugin.
	 *
	 * @param array $data Options for the function.
	 * @return array
	 */
	public function create_maps_redirect( $data ) {

		$target       = $data['target'];
		$creds        = 'SECRET';
		$auth_headers = array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Basic ' . base64_encode( $this->username . ':' . $this->password ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		);

		$all_redirects  = array();
		$redirects_res  = wp_remote_get( $this->url . '/wp-json/redirection/v1/redirect', array( 'headers' => $auth_headers ) );
		$redirects_body = json_decode( $redirects_res['body'], true );
		$redirects      = $redirects_body['items'];
		foreach ( $redirects as $redirect ) {
			$all_redirects[] = $redirect['url'];
		}

		do {
			$slug = $this->generate_random_letters( 5 );
			// A slug will be generated as long as the most recent value was not unique.
		} while ( ! is_unique( $slug, $all_redirects ) );

			$body               = array(
				'status'      => 'enabled',
				'position'    => 0,
				'match_data'  => array(
					'source' => array(
						'flag_regex'    => false,
						'flag_query'    => 'ignore',
						'flag_case'     => true,
						'flag_trailing' => false,
					),
				),
				'options'     => array(
					'log_exclude' => false,
				),
				'regex'       => false,
				'url'         => '/' . $slug,
				'match_type'  => 'url',
				'title'       => 'Maps Redirect',
				'group_id'    => 3,
				'action_type' => 'url',
				'action_code' => 301,
				'action_data' => array(
					'url' => $target,
				),
			);
			$create_res         = wp_remote_post(
				$this->url . '/wp-json/redirection/v1/redirect',
				array(
					'method'  => 'POST',
					'headers' => $auth_headers,
					'body'    => wp_json_encode( $body ),
				)
			);
			$create_res['body'] = json_decode( $create_res['body'] );
			return $create_res;

	}

	/**
	 * Returns random letters for a given length.
	 *
	 * @param int $length How many letters to return.
	 * @return string
	 */
	private function generate_random_letters( $length ) {
		$random = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$random .= chr( wp_rand( ord( 'a' ), ord( 'z' ) ) );
		}
		return $random;
	}

}

<?php

class WXAPI_REST_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'v1';
		$this->rest_base = 'wx';
	}

	/**
	 * Serve wechat events
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function serve($request) {

		$wx = new WeixinAPI();

		if ($request->get_param('echostr')) {
			return rest_ensure_response($wx->verify());
		}

	}

	/**
	 * Get JSAPI args
	 *
	 * @param WP_REST_Request $request
	 * @return mixed|WP_REST_Response
	 */
	public static function getJsapiArgs($request) {
		$wx = new WeixinAPI(true);
		$url = $request->get_param('url');
		error_log('getjsapiargeurl:' . $url);
		$args = $wx->generate_jsapi_args($url ?: null);
		return rest_ensure_response($args);
	}

	/**
	 * get mini program session from code
	 *
	 * @param WP_REST_Request $request
	 * @return mixed|WP_REST_Response
	 */
	public static function jsCodeToSession($request) {
		$code = $request->get_param('code');
		$wx = new WeixinAPI(true);
		$result = $wx->js_code_to_session($code);
		return rest_ensure_response($result);
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route($this->namespace, $this->rest_base, array(
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array($this, 'serve'),
			)
		));

		register_rest_route($this->namespace, $this->rest_base . '/jsapi-args', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array($this, 'getJsapiArgs'),
			)
		));

		register_rest_route($this->namespace, $this->rest_base . '/auth/code-to-session', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array($this, 'jsCodeToSession'),
			)
		));
	}

}

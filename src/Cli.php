<?php

namespace BernskioldMedia\WP\Concierge;

use DeepL\Translator;
use \WP_CLI;
use WP_Error;
use WP_Query;
use function get_field;
use function glob;
use function wp_handle_sideload;

class Cli
{

	/**
	 * Imports images from a folder structure to entertainments.
	 *
	 * ## OPTIONS
	 *
	 * <path>
	 * : Full path to the file we want to translate
	 * <source>
	 * : Source language
	 * <target>
	 * : Target language
	 *
	 * @subcommand deepl-translate
	 */
	public function deepl_translate($args): void
	{
		require_once(ABSPATH . 'wp-admin/includes/file.php');

		$path = $args[0];
		$source_lang = $args[1];
		$target_lang = $args[2];

		WP_CLI::log('Start translating ' . $path . ' from ' . $source_lang . ' to ' . $target_lang);

		if( !defined('DEEPL_AUTH_KEY') ) {
			WP_CLI::error('DEEPL_AUTH_KEY is not defined.');

			return;
		}

		$translator = new Translator(DEEPL_AUTH_KEY);

		var_dump($path);

		// Translate a formal document from English to German:
		try {
			$document = $translator->translateDocument(
				$path,
				'translated-doc.xliff',
				$source_lang,
				$target_lang,
			);
		var_dump($document);
		} catch (\DeepL\DocumentTranslationException $error) {
			echo 'Error occurred while translating document: ' . ($error->getMessage() ?? 'unknown error');
		}


		die;

		error_log($path);

		$response = wp_remote_post('https://api.deepl.com/v2/document',
		[
			'method'    => 'POST',
			'headers' => array(
				'Authorization' => 'DeepL-Auth-Key ' . base64_encode( DEEPL_AUTH_KEY ),
				'Content-Type' => 'multipart/form-data'
			),
			'body' =>json_encode( array(
				'source_lang' => $source_lang,
				'target_lang' => $target_lang,
				'file' => $path,
			))
		]);

		if ( is_wp_error( $response ) ) {
			WP_CLI::error('Error: ' . $response->get_error_message());
		}

		error_log(print_r($response, true));
		die;

		if( !$response['document_id'] && !$response['document_key'] ) {
			WP_CLI::error('Error: Could not get document_id and/or document_key.');
		}

		$document_id = $response['document_id'];
		$document_key = $response['document_key'];

		$status = $this->check_status($document_id, $document_key);

		while( $status['status'] !== 'done' &&  $status['status'] !== 'error') {
			WP_CLI::log('Status: ' . $status['status'] . '. Time remaining: ' . $status['time_remaining_seconds'] . ' seconds');

			sleep(5);
			$status = $this->check_status($document_id, $document_key);
		}
		if( $status['status'] === 'error' ){
			WP_CLI::error('Error: Could not translate file: ' . $status['error_message']);
		}

		if( $status['status'] === 'done' ){
			WP_CLI::log('Status: ' . $status['status'] . '. Time remaining: ' . $status['time_remaining_seconds'] . ' seconds');
		}


		WP_CLI::success('Finished importing images.');
	}

	public function check_status($document_id, $document_key){
		$response = wp_remote_post(esc_url('https://api.deepl.com/v2/document/'.$document_id),
			[
				'method'    => 'POST',
				'headers' => array(
					'Authorization' => 'DeepL-Auth-Key ' . base64_encode( DEEPL_AUTH_KEY ),
				),
				'data' => array(
					'document_key' => $document_key,
				)
			]);

		return $response;
	}

	public function download_translated_file($document_id, $document_key){
		$response = wp_remote_post(esc_url('https://api.deepl.com/v2/document/'.$document_id.'/result'),
			[
				'method'    => 'POST',
				'headers' => array(
					'Authorization' => 'DeepL-Auth-Key ' . base64_encode( DEEPL_AUTH_KEY ),
				),
				'data' => array(
					'document_key' => $document_key,
				)
			]);

		return $response;
	}
}

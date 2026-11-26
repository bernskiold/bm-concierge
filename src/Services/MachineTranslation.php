<?php

namespace BernskioldMedia\WP\Concierge\Services;

class MachineTranslation extends BaseService {

	public string $key = 'machine_translation';

	public function __construct() {
		parent::__construct();
		$this->title = __( 'Machine Translation', 'bm-concierge' );
	}

	public function get_cost( int $words ): float {
		$word_price = $this->data->wordPricing->{$this->get_currency()};

		return ( $words * $word_price );
	}

	public function order( array $data = [] ) {
		parent::order( $data ); // Processes the data and sends confirmation.

		$translated = $this->get_translated_data( $data['postId'], $data['targetLanguage'] );
		$this->save_to_post( $data['postId'], $translated['title'], $translated['content'] );
	}

	public function get_translated_data( int $post_id, string $targetLanguage ): array {
		$api_key = defined( 'BM_CONCIERGE_ML_TRANSLATION_API_KEY' ) ? BM_CONCIERGE_ML_TRANSLATION_API_KEY : null;

		if ( ! $api_key ) {
			return [
				'title'   => null,
				'content' => null,
			];
		}

		$translator = new \DeepL\DeepLClient( $api_key );
		$post       = get_post( $post_id );

		$options = [
			'tag_handling'    => 'html',
			'split_sentences' => 'nonewlines',
		];

		return [
			'title'   => $translator->translateText( $post->post_title, null, $targetLanguage, $options ),
			'content' => $translator->translateText( $post->post_content, null, $targetLanguage, $options ),
		];
	}

	public function save_to_post( int $post_id, ?string $title = null, ?string $content = null ): void {
		$args       = [];
		$args['ID'] = $post_id;

		if ( $title ) {
			$args['post_title'] = $title;
		}

		if ( $content ) {
			$args['post_content'] = $content;
		}

		wp_update_post( $args );
	}
}

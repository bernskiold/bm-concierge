<?php

namespace BernskioldMedia\WP\Concierge\Services;

abstract class BaseService {

	public string        $title;
	public string        $key;
	public ?object       $data;
	public static string $order_email;
	public object        $order_data;

	public function __construct() {
		$this->get_data();
	}

	public function order( array $data = [] ) {
		$this->order_data = (object) array_merge( $data, [
			'pageName'    => get_the_title( $data['postId'] ?? 0 ),
			'pageUrl'     => get_edit_post_link( $data['postId'] ?? 0 ),
			'websiteName' => get_bloginfo( 'name' ),
			'user'        => wp_get_current_user(),
		] );

		$this->send_order();
		$this->send_order_confirmation();
	}

	protected function get_data(): void {
		$api_url = apply_filters( 'bm/concierge/pricing_api_url', 'https://static.bernskioldmedia.com/concierge/api.json', $this->key );

		$data = get_transient( 'bm_concierge_products' );

		if ( false === $data ) {
			$response = wp_remote_get( $api_url );

			// Don't cache failures, so the next request tries again.
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				$this->data = null;

				return;
			}

			$data = wp_remote_retrieve_body( $response );

			set_transient( 'bm_concierge_products', $data, 5 * MINUTE_IN_SECONDS );
		}

		$data     = json_decode( (string) $data );
		$services = is_object( $data ) && isset( $data->services ) && is_array( $data->services ) ? $data->services : [];
		$services = array_filter( $services, function ( $service ) {
			return isset( $service->name ) && $service->name === $this->key;
		} );

		$this->data = $services[ array_key_first( $services ) ] ?? null;
	}

	protected function get_order_internal_email_subject(): string {
		return sprintf( 'New %1$s Concierge Order for %2$s', $this->title, $this->order_data->websiteName );
	}

	protected function get_order_internal_email_message(): string {
		ob_start(); ?>
		<p>There's a new BM Concierge order to manage.</p>
		<p><strong>Service:</strong> <?php
			echo $this->title; ?></p>
		<p><strong>Page:</strong> <a href="<?php
			echo $this->order_data->pageUrl; ?>"><?php
				echo $this->order_data->pageName; ?></a></p>
		<p><strong>Delivery:</strong> <?php
			echo $this->order_data->express ? 'Express' : 'Standard'; ?></p>
		<p><strong>Price:</strong> <?php
			echo $this->order_data->price; ?></p>
		<p><strong>Website Name:</strong> <?php
			echo $this->order_data->websiteName; ?></p>
		<?php
		$this->get_order_internal_email_message_extra_meta(); ?>
		<?php
		return ob_get_clean();
	}

	protected function get_order_internal_email_message_extra_meta(): void {
	}

	protected function get_order_confirmation_email_subject(): string {
		return __( 'Your Concierge Order Confirmation', 'bm-concierge' );
	}

	protected function get_order_confirmation_email_message(): string {
		ob_start();
		?>
		<p><?php
			printf( __( 'Hi %s,', 'bm-concierge' ), $this->order_data->user->first_name ); ?></p>
		<p><?php
			esc_html_e( 'Thanks for using Bernskiold Media Concierge.', 'bm-concierge' ); ?></p>
		<p><?php
			printf( __( 'We have received your order for %1$s for "%2$s" on %3$s. As soon as it is ready, we will let you know.', 'bm-concierge' ), $this->title,
				$this->order_data->pageName, $this->order_data->websiteName ); ?></p>
		<?php
		if ( $this->order_data->express ) : ?>
			<p><?php
				esc_html_e( 'You\'ve selected express delivery for this job.', 'bm-concierge' ); ?></p>
		<?php
		else : ?>
			<p><?php
				esc_html_e( 'You\'ve selected standard delivery for this job.', 'bm-concierge' ); ?></p>
		<?php
		endif; ?>
		<p><?php
			printf( __( 'The price for your concierge job will be %s, and will be invoiced separately.', 'bm-concierge' ), $this->order_data->price ); ?></p>
		<p><?php
			esc_html_e( 'Best Regards,', 'bm-concierge' ); ?><br><?php
			esc_html_e( 'Bernskiold Media', 'bm-concierge' ); ?></p>
		<?php
		return ob_get_clean();
	}

	protected function send_order(): void {
		wp_mail( 'support@bernskioldmedia.com', $this->get_order_internal_email_subject(), $this->get_order_internal_email_message(), [
			'From: Bernskiold Media Concierge <concierge@bernskioldmedia.com>',
			"Reply-To: {$this->order_data->user->first_name} {$this->order_data->user->last_name} <{$this->order_data->user->user_email}>",
			'Content-Type: text/html; charset=UTF-8',
		] );
	}

	protected function send_order_confirmation(): void {
		wp_mail( $this->order_data->user->user_email, $this->get_order_confirmation_email_subject(), $this->get_order_confirmation_email_message(), [
			'From: Bernskiold Media Concierge <concierge@bernskioldmedia.com>',
			'Content-Type: text/html; charset=UTF-8',
		] );
	}

	public function get_currency(): string {
		switch ( get_locale() ) {
			case 'sv_SE':
				$currency = 'SEK';
				break;

			case 'en_GB':
				$currency = 'GBP';
				break;

			default:
				$currency = 'USD';
				break;
		}

		return apply_filters( 'bm/concierge/currency', $currency, $this->key );
	}
}

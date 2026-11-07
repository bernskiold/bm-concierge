import {
	Button,
	PanelBody,
	Spinner,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { useOrder } from '../../hooks/order';
import { useOnPagePrice } from '../../hooks/price';
import OrderModal from '../order-modal';

export default function OnPage() {
	const {
		orderModalOpen,
		orderSubmitted,
		placeOrder,
		closeOrderModal,
		openOrderModal,
	} = useOrder();
	const [ isExpress, setIsExpress ] = useState( false );
	const [ keyword, setKeyword ] = useState( null );
	const {
		formattedNormalPrice,
		formattedExpressPrice,
		formattedExpressPriceExtra,
	} = useOnPagePrice();

	const price = isExpress ? formattedExpressPrice : formattedNormalPrice;

	const title = () => {
		if ( formattedNormalPrice ) {
			return sprintf(
				/* translators: %s: formatted price */
				__( 'SEO Optimization: %s', 'bm-concierge' ),
				formattedNormalPrice
			);
		}

		return (
			<>
				{ /* eslint-disable-next-line @wordpress/i18n-no-flanking-whitespace -- kept to preserve existing translations. */ }
				{ __( 'SEO Optimization: ', 'bm-concierge' ) }
				<Spinner />
			</>
		);
	};

	return (
		<PanelBody title={ title() } initialOpen={ false }>
			<p>
				{ __(
					'Make your page rank well in search engines by on-page SEO optimization.',
					'bm-concierge'
				) }
			</p>
			<ul>
				<li>
					<strong>{ __( 'Turnaround', 'bm-concierge' ) }:</strong>{ ' ' }
					{ __( '72 hours (24h express)', 'bm-concierge' ) }
				</li>
				<li>
					<strong>{ __( 'Price', 'bm-concierge' ) }:</strong>{ ' ' }
					{ formattedNormalPrice }
				</li>
			</ul>
			<Button variant="secondary" onClick={ openOrderModal }>
				{ __( 'Proceed to Order', 'bm-concierge' ) }
			</Button>
			<OrderModal
				title={ __( 'SEO Optimization Concierge', 'bm-concierge' ) }
				isOpen={ orderModalOpen }
				onClose={ closeOrderModal }
				hasBeenSubmitted={ orderSubmitted }
				submitButtonText={ __(
					'Send for SEO Optimization',
					'bm-concierge'
				) }
				onSubmit={ () => {
					placeOrder( {
						type: 'onpage',
						express: isExpress,
						price,
						keyword,
					} );
				} }
			>
				<p>
					{ __(
						'On-page SEO optimization boosts the ranking for the page in search engines. Based on your keyword, we will adjust the page for optimal ranking. Tweaking copy, optimizing titles and descriptions for Google and social sharing and taking care of more technical factors.',
						'bm-concierge'
					) }
				</p>
				<p>
					{ __(
						"We'll have the page ready for you in 72 hours, right here in the editor. In a hurry? Add the express option and have it ready within 24 hours.",
						'bm-concierge'
					) }
				</p>
				<hr className="bm-concierge-divider" />
				<TextControl
					label={ __( 'Target Search Keyword', 'bm-concierge' ) }
					onChange={ setKeyword }
					help={ __(
						'We need to know what search keyword you want your page to rank for in Google. We\ll check to make sure that it is reasonable before we do the optimization.',
						'bm-concierge'
					) }
				/>
				<ToggleControl
					label={ sprintf(
						/* translators: %s: formatted price */
						__( 'Express Delivery (%s)', 'bm-concierge' ),
						formattedExpressPriceExtra
					) }
					help={
						isExpress
							? __(
									"You've chosen express. You will have it ready in 24 hours.",
									'bm-concierge'
								)
							: __(
									'Without express, you will have the page ready in 72 hours.',
									'bm-concierge'
								)
					}
					onChange={ () => setIsExpress( ! isExpress ) }
					checked={ isExpress === true }
				/>
				<p>
					{ __( 'Your Price:', 'bm-concierge' ) }{ ' ' }
					<strong>{ price }</strong>
				</p>
			</OrderModal>
		</PanelBody>
	);
}

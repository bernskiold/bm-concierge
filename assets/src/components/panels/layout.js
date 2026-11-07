import {
	Button,
	PanelBody,
	Spinner,
	ToggleControl,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { useOrder } from '../../hooks/order';
import { useLayoutPrice } from '../../hooks/price';
import OrderModal from '../order-modal';

export default function Layout() {
	const {
		orderModalOpen,
		orderSubmitted,
		placeOrder,
		closeOrderModal,
		openOrderModal,
	} = useOrder();
	const [ isExpress, setIsExpress ] = useState( false );
	const {
		formattedNormalPrice,
		formattedExpressPrice,
		formattedExpressPriceExtra,
	} = useLayoutPrice();
	const price = isExpress ? formattedExpressPrice : formattedNormalPrice;

	const title = () => {
		if ( formattedNormalPrice ) {
			return sprintf(
				/* translators: %s: formatted price */
				__( 'Layout: %s', 'bm-concierge' ),
				formattedNormalPrice
			);
		}

		return (
			<>
				{ /* eslint-disable-next-line @wordpress/i18n-no-flanking-whitespace -- kept to preserve existing translations. */ }
				{ __( 'Layout: ', 'bm-concierge' ) }
				<Spinner />
			</>
		);
	};

	return (
		<PanelBody title={ title() } initialOpen={ false }>
			<p>
				{ __(
					'Good design leaves a lasting impression and converts visitors. Let us help you create an engaging layout for this page.',
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
				title={ __( 'Layout Concierge', 'bm-concierge' ) }
				isOpen={ orderModalOpen }
				onClose={ closeOrderModal }
				hasBeenSubmitted={ orderSubmitted }
				submitButtonText={ __( 'Send for Layouting', 'bm-concierge' ) }
				onSubmit={ () => {
					placeOrder( {
						type: 'layout',
						express: isExpress,
						price,
					} );
				} }
			>
				<p>
					{ __(
						'Using the full range of visuals on your website we\ll make this page look interesting, great and converting.',
						'bm-concierge'
					) }
				</p>
				<p>
					{ __(
						"We won't create illustrations, graphics or other media as part of this. But we may suggest it if we think it will benefit the page.",
						'bm-concierge'
					) }
				</p>
				<p>
					{ __(
						"We'll have the text back to you in 72 hours, right here in the editor. In a hurry? Add the express option and get it back within 24 hours.",
						'bm-concierge'
					) }
				</p>
				<hr className="bm-concierge-divider" />
				<ToggleControl
					label={ sprintf(
						/* translators: %s: formatted price */
						__( 'Express Delivery (%s)', 'bm-concierge' ),
						formattedExpressPriceExtra
					) }
					help={
						isExpress
							? __(
									"You've chosen express. Your page layout will be done in 24 hours.",
									'bm-concierge'
								)
							: __(
									'Without express, your page layout will be done in 72 hours.',
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

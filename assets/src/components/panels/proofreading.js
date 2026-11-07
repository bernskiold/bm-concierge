import {
	Button,
	PanelBody,
	Spinner,
	ToggleControl,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { useOrder } from '../../hooks/order';
import { useProofreadingPrice } from '../../hooks/price';
import OrderModal from '../order-modal';

export default function Proofreading() {
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
	} = useProofreadingPrice();

	const price = isExpress ? formattedExpressPrice : formattedNormalPrice;

	const title = () => {
		if ( formattedNormalPrice ) {
			return sprintf(
				/* translators: %s: formatted price */
				__( 'Proofreading: %s', 'bm-concierge' ),
				formattedNormalPrice
			);
		}

		return (
			<>
				{ /* eslint-disable-next-line @wordpress/i18n-no-flanking-whitespace -- kept to preserve existing translations. */ }
				{ __( 'Proofreading: ', 'bm-concierge' ) }
				<Spinner />
			</>
		);
	};

	return (
		<PanelBody title={ title() } initialOpen={ false }>
			<p>
				{ __(
					'Ready to publish but want a second eye? Let us proof your content to free it from mistakes.',
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
				title={ __( 'Proofreading Concierge', 'bm-concierge' ) }
				isOpen={ orderModalOpen }
				onClose={ closeOrderModal }
				hasBeenSubmitted={ orderSubmitted }
				submitButtonText={ __(
					'Send for Proofreading',
					'bm-concierge'
				) }
				onSubmit={ () => {
					placeOrder( {
						type: 'proofreading',
						express: isExpress,
						price,
					} );
				} }
			>
				<p>
					{ __(
						'Proofreading includes catching any errors and making sure the content adheres to a well-formed style.',
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
									"You've chosen express. You will get your content back in 24 hours.",
									'bm-concierge'
								)
							: __(
									'Without express, you will get your text back in 72 hours.',
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

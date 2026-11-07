import {
	Button,
	PanelBody,
	Spinner,
	ToggleControl,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { useOrder } from '../../hooks/order';
import { useAccessibilityPrice } from '../../hooks/price';
import OrderModal from '../order-modal';

export default function AccessibilityReview() {
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
	} = useAccessibilityPrice();

	const price = isExpress ? formattedExpressPrice : formattedNormalPrice;

	const title = () => {
		if ( formattedNormalPrice ) {
			return sprintf(
				/* translators: %s: formatted price */
				__( 'Accessibility Audit: %s', 'bm-concierge' ),
				formattedNormalPrice
			);
		}

		return (
			<>
				{ /* eslint-disable-next-line @wordpress/i18n-no-flanking-whitespace -- kept to preserve existing translations. */ }
				{ __( 'Accessibility Audit: ', 'bm-concierge' ) }
				<Spinner />
			</>
		);
	};

	return (
		<PanelBody title={ title() } initialOpen={ false }>
			<p>
				{ __(
					"Confidently meet WCAG accessibility criteria. We'll analyze your content and return to you a checklist of things to address to meet the criteria.",
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
				title={ __( 'Accessibility Review Concierge', 'bm-concierge' ) }
				isOpen={ orderModalOpen }
				onClose={ closeOrderModal }
				hasBeenSubmitted={ orderSubmitted }
				submitButtonText={ __(
					'Start Accessibility Audit',
					'bm-concierge'
				) }
				onSubmit={ () => {
					placeOrder( {
						type: 'accessibility-review',
						express: isExpress,
						price,
					} );
				} }
			>
				<p>
					{ __(
						'Publishing accessible content is important. In some sectors even required. An accessibility audit on this page against WCAG 2.1 AA includes all content and documents on it. You get a comprehensive audit protocol showing you what to improve and what is already good.',
						'bm-concierge'
					) }
				</p>
				<p>
					{ __(
						"We'll have the analysis back to you in 72 hours via email. In a hurry? Add the express option and get it back within 24 hours.",
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
									"You've chosen express. You will get the analysis in 24 hours.",
									'bm-concierge'
								)
							: __(
									'Without express, you will get the analysis in 72 hours.',
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

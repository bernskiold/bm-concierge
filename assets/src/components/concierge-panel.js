import { Button, Spinner } from '@wordpress/components';
import { dispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import {
	useAccessibilityPrice,
	useLayoutPrice,
	useMachineTranslationPrice,
	useOnPagePrice,
	useProofreadingPrice,
} from '../hooks/price';
import { useServices } from '../hooks/services';

export default function ConciergePanel() {
	const {
		isLayoutEnabled,
		isOnpageEnabled,
		isProofreadingEnabled,
		isAccessibilityEnabled,
		isMachineTranslationEnabled,
		hasLoadedServices,
	} = useServices();
	const { formattedNormalPrice: proofreadingPrice } = useProofreadingPrice();
	const { formattedNormalPrice: onPagePrice } = useOnPagePrice();
	const { formattedNormalPrice: accessibilityPrice } =
		useAccessibilityPrice();
	const { formattedNormalPrice: layoutPrice } = useLayoutPrice();
	const { formattedNormalPrice: machineTranslationPrice } =
		useMachineTranslationPrice();

	const openConciergeSidebar = () => {
		dispatch( 'core/edit-post' ).openGeneralSidebar(
			'bm-concierge/sidebar'
		);
	};

	return (
		<>
			<p>
				{ __(
					"Sometimes you could use an extra hand. We'll help you make your content and website be the best it can be.",
					'bm-concierge'
				) }
			</p>
			{ hasLoadedServices === 0 ? (
				<Spinner />
			) : (
				<>
					<ul>
						{ isAccessibilityEnabled && (
							<li>
								<strong>
									{ __(
										'Accessibility Audit',
										'bm-concierge'
									) }
									:
								</strong>{ ' ' }
								{ accessibilityPrice ? (
									accessibilityPrice
								) : (
									<Spinner />
								) }
							</li>
						) }
						{ isOnpageEnabled && (
							<li>
								<strong>
									{ __( 'SEO Optimization', 'bm-concierge' ) }
									:
								</strong>{ ' ' }
								{ onPagePrice ? onPagePrice : <Spinner /> }
							</li>
						) }
						{ isProofreadingEnabled && (
							<li>
								<strong>
									{ __( 'Proofreading', 'bm-concierge' ) }:
								</strong>{ ' ' }
								{ proofreadingPrice ? (
									proofreadingPrice
								) : (
									<Spinner />
								) }
							</li>
						) }
						{ isLayoutEnabled && (
							<li>
								<strong>
									{ __( 'Layout', 'bm-concierge' ) }:
								</strong>{ ' ' }
								{ layoutPrice ? layoutPrice : <Spinner /> }
							</li>
						) }
						{ isMachineTranslationEnabled && (
							<li>
								<strong>
									{ __(
										'Machine Translation',
										'bm-concierge'
									) }
									:
								</strong>{ ' ' }
								{ machineTranslationPrice ? (
									machineTranslationPrice
								) : (
									<Spinner />
								) }
							</li>
						) }
					</ul>
					<Button
						onClick={ openConciergeSidebar }
						variant="secondary"
					>
						{ __( 'Open Concierge', 'bm-concierge' ) }
					</Button>
					<p className="bm-concierge-disclaimer">
						{ __(
							'Some prices may depend on how much content you have. As you update the page, the price may update.',
							'bm-concierge'
						) }
					</p>
				</>
			) }
		</>
	);
}

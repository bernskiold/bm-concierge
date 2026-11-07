import { PanelBody, Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useServices } from '../hooks/services';
import AccessibilityReview from './panels/accessibility-review';
import Layout from './panels/layout';
import MachineTranslation from './panels/machine-translation';
import OnPage from './panels/onpage';
import ProofreadingPanel from './panels/proofreading';

export default function ConciergeSidebar() {
	const {
		hasLoadedServices,
		isAccessibilityEnabled,
		isOnpageEnabled,
		isProofreadingEnabled,
		isLayoutEnabled,
		isMachineTranslationEnabled,
	} = useServices();

	if ( ! hasLoadedServices ) {
		return (
			<>
				<ConciergeHeader />
				<PanelBody>
					<Spinner />
				</PanelBody>
			</>
		);
	}

	return (
		<>
			<ConciergeHeader />
			{ isAccessibilityEnabled && <AccessibilityReview /> }
			{ isOnpageEnabled && <OnPage /> }
			{ isProofreadingEnabled && <ProofreadingPanel /> }
			{ isLayoutEnabled && <Layout /> }
			{ isMachineTranslationEnabled && <MachineTranslation /> }
			<PanelBody>
				<p className="bm-concierge-disclaimer">
					{ __(
						'Some prices may depend on how much content you have. As you update the page, the price may update.',
						'bm-concierge'
					) }
				</p>
				<p className="bm-concierge-disclaimer">
					{ __(
						'All prices apply to this specific page of content only. Not the entire website.',
						'bm-concierge'
					) }
				</p>
			</PanelBody>
		</>
	);
}

const ConciergeHeader = () => (
	<PanelBody>
		<p>
			{ __(
				"Sometimes you could use an extra hand. We'll help you make your content and website be the best it can be.",
				'bm-concierge'
			) }
		</p>
	</PanelBody>
);
